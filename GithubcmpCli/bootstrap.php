<?php

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 05.01.2016 18:51.
 */
require_once __DIR__.'/../vendor/autoload.php';

use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerAutoloadNamespace('Githubcmp', __DIR__.'/../vendor/ostretsov/githubcmp');
