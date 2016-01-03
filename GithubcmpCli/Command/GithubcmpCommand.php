<?php

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 03.01.2016 13:55
 */

namespace GithubcmpCli\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GithubcmpCommand extends Command
{
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

    }
}