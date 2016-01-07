<?php

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 07.01.2016 08:47.
 */

namespace GithubcmpCli\Renderer;

use Github\Client;
use Githubcmp\Model\Repository;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class GistRenderer extends CliRenderer
{
    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var BufferedOutput
     */
    protected $bufferedOutput;

    public function __construct(OutputInterface $output, $apiToken)
    {
        if (!$apiToken) {
            throw new \InvalidArgumentException('Github API token must be specified!');
        }

        $this->apiToken = $apiToken;
        $this->output = $output;
        $this->bufferedOutput = new BufferedOutput();

        parent::__construct($this->bufferedOutput);
    }

    /**
     * @param Repository[] $repositories
     * @param array        $options
     */
    public function render(array $repositories, array $options)
    {
        parent::render($repositories, $options);
        $content = $this->bufferedOutput->fetch();
        $this->output->writeln($content);

        $this->output->writeln('Publishing on gist.github.com...');
        $client = new Client();
        $client->authenticate($this->apiToken, null, Client::AUTH_HTTP_TOKEN);
        $response = $client->api('gists')->create([
            'public' => true,
            'description' => $this->getDescription($repositories),
            'files' => [
                $this->getFilename($repositories) => [
                    'content' => $this->getDecoratedContent($content),
                ],
            ],
        ]);

        $this->output->writeln('Gist URL: %s', $response['url']);
    }

    private function getFilename(array $repositories)
    {
        $file = array_map(function ($repository) { return $repository->repository; }, $repositories);

        return sprintf('%s_%s.md', implode('_', $file), time());
    }

    private function getDecoratedContent($consoleOutput)
    {
        return <<<"GIST"
```
$consoleOutput
```
GIST;
    }
}
