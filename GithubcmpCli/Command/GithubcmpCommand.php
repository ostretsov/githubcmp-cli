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

class GithubcmpCommand extends Command
{
    /**
     * @var Repository[]
     */
    private $repositories;

    protected function configure()
    {
        $this
            ->setName('ostretsov:githubcmp')
            ->setDescription('Compare github.com repositories')
            ->addOption(
                'token',
                null,
                InputOption::VALUE_OPTIONAL,
                'Github API token to use'
            )
        ;
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
            $urlQuestion = new Question($i.'. Please enter the URL of repository on github.com: ');
            $url = $questionHelper->ask($input, $output, $urlQuestion);
            $urlParts = explode('/', $url);
            if (count($urlParts) != 2) {
                $invalidUrl = $formatterHelper->formatBlock(sprintf('"%s" is invalid URL! Enter something like "ostretsov/githubcmp" (without quotes).', $url), 'error');
                $output->writeln($invalidUrl);

                continue;
            }

            list($username, $repository) = $urlParts;
            try {
                $this->repositories[] = $repositoryBuilder->build($username, $repository)->getResult();
            } catch (RuntimeException $e) {
                $notFound = $formatterHelper->formatBlock(sprintf('"%s" is not found!', $url), 'error');
                $output->writeln($notFound);

                continue;
            }

            ++$i;
        } while (count($this->repositories) < 2 || $questionHelper->ask($input, $output, $continueQuestion));

        $comparator = new Comparator();
        $sortedRepositories = $comparator->compare($this->repositories);

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
                        $rows[] = [$property->name, $propertyAccessor->getValue($repository, $property->name), $annotation->value];
                    }
                }
            }

            $resultTable = new Table($output);
            $resultTable
                ->setHeaders(['Key', 'Value', 'Factor'])
                ->setRows($rows)
            ;
            $resultTable->render();

            ++$i;
        }

        // finalize output
        $output->writeln('');
        $output->writeln(sprintf('The number of requests remaining in the current rate limit window: %s.', $repositoryBuilder->getClient()->getHttpClient()->getLastResponse()->getHeader('X-RateLimit-Remaining')->__toString()));
    }
}
