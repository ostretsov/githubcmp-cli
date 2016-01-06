<?php

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 06.01.2016 07:17.
 */

namespace GithubcmpCli\Renderer;

abstract class AbstractRenderer
{
    abstract public function render(array $repositories);
}
