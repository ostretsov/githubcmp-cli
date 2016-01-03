#!/usr/bin/env php
<?php

require_once __DIR__.'/vendor/autoload.php';

use GithubcmpCli\Command\GithubcmpCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

$application = new Application();
$application->add(new GithubcmpCommand());
//$argv = $_SERVER['argv'];
//$firstArgument = array_shift($argv);
//array_unshift($argv, $firstArgument, 'ostretsov:githubcmp');
//$input = new ArgvInput($argv);
$application->run();