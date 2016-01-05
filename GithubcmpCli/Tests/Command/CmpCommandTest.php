<?php

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 05.01.2016 18:37.
 */

namespace GithubcmpCli\Tests\Command;

use GithubcmpCli\Command\CmpCommand;

class CmpCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group unit
     */
    public function testGetUniqueOptions()
    {
        $fullNamedOptions = [
            'size',
            'stargazersCount',
            'forks',
            'openIssues',
            'subscribersCount',
            'userPublicRepos',
            'commitsCount',
            'commitsLastMonthCount',
            'avgCommitsPerWeek',
            'contributorsCount',
        ];

        $expectedOptions = [
            'size' => 's',
            'stargazersCount' => 'st',
            'forks' => 'f',
            'openIssues' => 'o',
            'subscribersCount' => 'su',
            'userPublicRepos' => 'u',
            'commitsCount' => 'c',
            'commitsLastMonthCount' => 'co',
            'avgCommitsPerWeek' => 'a',
            'contributorsCount' => 'con',
        ];

        $cmpCommand = new CmpCommand();
        $result = $cmpCommand->getUniqueOptions($fullNamedOptions);
        foreach ($expectedOptions as $key => $expectedOptionShortcut) {
            $this->assertEquals($expectedOptionShortcut, $result[$key]);
        }

        $fullNamedOptions = [
            'size',
            's',
        ];

        $expectedOptions = [
            'size' => 's',
            's' => 'sx',
        ];

        $result = $cmpCommand->getUniqueOptions($fullNamedOptions);
        foreach ($expectedOptions as $key => $expectedOptionShortcut) {
            $this->assertEquals($expectedOptionShortcut, $result[$key]);
        }
    }
}
