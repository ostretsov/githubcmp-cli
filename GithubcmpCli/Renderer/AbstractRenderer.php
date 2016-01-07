<?php

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 06.01.2016 07:17.
 */

namespace GithubcmpCli\Renderer;

use Githubcmp\Model\Repository;

abstract class AbstractRenderer
{
    abstract public function render(array $repositories, array $options);

    /**
     * @param Repository[] $repositories
     *
     * @return string
     */
    public function getDescription(array $repositories)
    {
        $description = array_map(function ($repository) { return $repository->username.'/'.$repository->repository; }, $repositories);

        return sprintf('Repositories comparison: %s.', implode(', ', $description));
    }
}
