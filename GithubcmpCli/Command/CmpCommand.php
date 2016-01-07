<?php

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 03.01.2016 13:55.
 */

namespace GithubcmpCli\Command;

use Doctrine\Common\Annotations\AnnotationReader;
use Github\Exception\RuntimeException;
use Githubcmp\Annotation\Weight;
use Githubcmp\Comparator;
use Githubcmp\Model\Repository;
use Githubcmp\RepositoryBuilder\GithubRepositoryBuilder;
use GithubcmpCli\Exception\NotImplementedException;
use GithubcmpCli\Renderer\CliRenderer;
use GithubcmpCli\Renderer\GistRenderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class CmpCommand extends Command
{
    const OUTPUT_TYPE_CLI = 'cli';
    const OUTPUT_TYPE_HTML = 'html';
    const OUTPUT_TYPE_GIST = 'gist';

    private $apiToken = null;

    private $supportedOutputTypes = [];

    private $outputType = self::OUTPUT_TYPE_CLI;

    private $availableWeightOptions = [];

    private $weightOptions = [];

    protected function configure()
    {
        $this
            ->setName('cmp')
            ->setDescription('Compare github repositories')
            ->addOption(
                'type',
                null,
                InputOption::VALUE_REQUIRED,
                'Supported outputs: cli (default), html, gist (token with "gist" scope is needed)',
                'cli'
            )
            ->addOption(
                'token',
                't',
                InputOption::VALUE_OPTIONAL,
                'Github API token to use'
            )
        ;

        $this->configureWeightOptions();
    }

    protected function configureWeightOptions()
    {
        // weight configurations
        $reflectedClass = new \ReflectionClass(Repository::class);
        $reader = new AnnotationReader();
        foreach ($reflectedClass->getProperties() as $property) {
            foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Weight) {
                    $this->availableWeightOptions[] = $property->getName();
                }
            }
        }

        foreach ($this->getUniqueOptions($this->availableWeightOptions) as $name => $shortcut) {
            $this->addOption($name, $shortcut, InputOption::VALUE_OPTIONAL);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareEnvironment($input, $output);
        $repositories = $this->getRepositories($input, $output);

        $comparator = new Comparator();
        $repositories = $comparator->compare($repositories, $this->weightOptions);
        $renderer = null;

        switch ($this->outputType) {
            case self::OUTPUT_TYPE_CLI:
                $renderer = new CliRenderer($output);
                break;
            case self::OUTPUT_TYPE_GIST:
                $renderer = new GistRenderer($output, $this->apiToken);
                break;
            case self::OUTPUT_TYPE_HTML:
                throw new NotImplementedException(sprintf('"%s" output format is not yet implemented!'));
        }

        $renderer->render($repositories, $this->weightOptions);
    }

    protected function prepareEnvironment(InputInterface $input, OutputInterface $output)
    {
        $reflectedClass = new \ReflectionClass(self::class);
        foreach ($reflectedClass->getConstants() as $name => $value) {
            if (0 === strpos($name, 'OUTPUT_TYPE_')) {
                $this->supportedOutputTypes[] = $value;
            }
        }

        $this->outputType = $input->getOption('type');
        if (!in_array($this->outputType, $this->supportedOutputTypes)) {
            $invalidOutputType = $this->getHelper('formatter')->formatBlock(
                sprintf(
                    '"%s" is invalid output type! Supported types: %s.',
                    $this->outputType,
                    implode(', ', $this->supportedOutputTypes)
                ),
                'error'
            );
            $output->writeln($invalidOutputType, 'error');

            exit(2);
        }

        $this->apiToken = $input->getOption('token');
        if (!$this->apiToken) {
            if ($this->outputType == self::OUTPUT_TYPE_GIST) {
                $tokenMustBeDefined = $this->getHelper('formatter')->formatBlock('Token with gist scope must be defined to publish on gist.github.com!', 'error');
                $output->writeln($tokenMustBeDefined);

                exit(1);
            }

            $tokenIsNotDefined = $this->getHelper('formatter')->formatBlock('Github API token is not specified! Requests amount is seriously limited. Specify token with --token option.', 'info');
            $output->writeln($tokenIsNotDefined);
        }

        foreach ($this->availableWeightOptions as $weightOption) {
            if ($input->hasOption($weightOption)) {
                $this->weightOptions[strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1_$2', $weightOption))] = floatval($input->getOption($weightOption));
            }
        }
        $this->weightOptions = array_filter($this->weightOptions, function ($e) { return $e > 0; });
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return Repository[]
     */
    protected function getRepositories(InputInterface $input, OutputInterface $output)
    {
        $formatterHelper = $this->getHelper('formatter');
        $questionHelper = $this->getHelper('question');

        $repositories = [];
        $continueQuestion = new ConfirmationQuestion('Add one more repository to compare? [y/n] ', false);
        $repositoryBuilder = new GithubRepositoryBuilder($this->apiToken);
        $i = 1;
        do {
            $urlQuestion = new Question($i.'. Please enter the name (username/repository) of repository on github.com: ');
            $url = $questionHelper->ask($input, $output, $urlQuestion);
            $urlParts = explode('/', $url);
            if (count($urlParts) != 2) {
                $invalidUrl = $formatterHelper->formatBlock(sprintf('"%s" is invalid repository name! Enter something like "ostretsov/githubcmp" (without quotes).', $url), 'error');
                $output->writeln($invalidUrl);

                continue;
            }

            list($username, $repository) = $urlParts;
            try {
                $output->writeln('Getting repository information...');
                $repositories[] = $repositoryBuilder->build($username, $repository)->getResult();
            } catch (RuntimeException $e) {
                $notFound = $formatterHelper->formatBlock(sprintf('"%s" is not found!', $url), 'error');
                $output->writeln($notFound);

                continue;
            }

            ++$i;
        } while (count($repositories) < 2 || $questionHelper->ask($input, $output, $continueQuestion));

        // remaining rate limit
        $output->writeln(
            sprintf(
                'The number of requests remaining in the current rate limit window: %s.',
                $repositoryBuilder->getClient()->getHttpClient()->getLastResponse()->getHeader('X-RateLimit-Remaining')
            )
        );

        return $repositories;
    }

    /**
     * TODO move somewhere.
     */
    public function getUniqueOptions(array $fullNamedOptions)
    {
        $shortcuts = [];
        foreach ($fullNamedOptions as $fullNamedOption) {
            $size = 1;
            do {
                $shortcut = substr($fullNamedOption, 0, $size);
                if ($size > strlen($fullNamedOption)) {
                    $shortcut .= 'x';
                }
                ++$size;
            } while (in_array($shortcut, $shortcuts));

            $shortcuts[] = $shortcut;
        }

        return array_combine($fullNamedOptions, $shortcuts);
    }
}
