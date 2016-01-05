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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CmpCommand extends Command
{
    private $weightOptions = [];

    /**
     * @var Repository[]
     */
    private $repositories;

    protected function configure()
    {
        $this
            ->setName('cmp')
            ->setDescription('Compare github repositories')
            ->addOption(
                'token',
                't',
                InputOption::VALUE_OPTIONAL,
                'Github API token to use'
            )
        ;

        // weight configurations
        $reflectedClass = new \ReflectionClass(Repository::class);
        $reader = new AnnotationReader();
        foreach ($reflectedClass->getProperties() as $property) {
            foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof Weight) {
                    $this->weightOptions[] = $property->getName();
                }
            }
        }

        foreach ($this->getUniqueOptions($this->weightOptions) as $name => $shortcut) {
            $this->addOption($name, $shortcut, InputOption::VALUE_OPTIONAL);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatterHelper = $this->getHelper('formatter');
        $questionHelper = $this->getHelper('question');

        $token = $input->getOption('token');
        if (!$token) {
            $tokenIsNotDefined = $formatterHelper->formatBlock('Github API token is not specified! Requests amount is seriously limited. Specify token with --token option.', 'info');
            $output->writeln($tokenIsNotDefined);
        }

        $continueQuestion = new ConfirmationQuestion('Add one more repository to compare? [y/n] ', false);
        $repositoryBuilder = new GithubRepositoryBuilder($token);
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
                $this->repositories[] = $repositoryBuilder->build($username, $repository)->getResult();
            } catch (RuntimeException $e) {
                $notFound = $formatterHelper->formatBlock(sprintf('"%s" is not found!', $url), 'error');
                $output->writeln($notFound);

                continue;
            }

            ++$i;
        } while (count($this->repositories) < 2 || $questionHelper->ask($input, $output, $continueQuestion));

        $options = [];
        foreach ($this->weightOptions as $weightOption) {
            if ($input->hasOption($weightOption)) {
                $options[strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1_$2', $weightOption))] = $input->getOption($weightOption);
            }
        }
        $comparator = new Comparator();
        $sortedRepositories = $comparator->compare($this->repositories, $options);

        // output results
        $i = 1;
        $reflectedClass = new \ReflectionClass(Repository::class);
        $reader = new AnnotationReader();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($sortedRepositories as $repository) {
            /* @var Repository $repository */
            $output->writeln('');
            $output->writeln(sprintf('%d. %s with %d%%', $i, $repository->username.'/'.$repository->repository, $repository->getRating()));
            $output->writeln('');

            // prepare rows for table rendering
            $rows = [];
            foreach ($reflectedClass->getProperties() as $property) {
                foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                    if ($annotation instanceof Weight) {
                        // TODO Move to Comparator (get* function)
                        $weight = isset($options[$property->name]) && $options[$property->name] > 0 ? $options[$property->name] : $annotation->value;
                        $rows[] = [
                            ucfirst(strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1 $2', $property->name))),
                            $propertyAccessor->getValue($repository, $property->name),
                            $weight,
                            $propertyAccessor->getValue($repository, $property->name) * $weight,
                        ];
                    }
                }
            }

            usort($rows, function ($a, $b) {
                return $b[3] - $a[3];
            });
            $rows[] = ['Total', '', '', $repository->getWeight()];

            $resultTable = new Table($output);
            $resultTable
                ->setHeaders(['Key', 'Value', 'Weight', 'Rating'])
                ->setRows($rows)
            ;
            $resultTable->render();
            $output->writeln(sprintf('Absolute rating: %01.2f', $repository->getWeight()));

            ++$i;
        }

        // finalize output
        $output->writeln('');
        $output->writeln(sprintf('The number of requests remaining in the current rate limit window: %s.', $repositoryBuilder->getClient()->getHttpClient()->getLastResponse()->getHeader('X-RateLimit-Remaining')->__toString()));
    }

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
