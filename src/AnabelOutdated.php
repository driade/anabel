<?php

namespace Driade\Anabel;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class AnabelOutdated
{
    private $app;
    protected $packages = array();

    public function setApp(Application $app)
    {
        $this->app = $app;
    }

    public function handle($all, $composer_dir)
    {
        $this->options = array('command' => 'outdated', '--format' => 'json');

        if ($all) {
            $this->options = array_merge($this->options, array('--all' => '--all'));
        }

        chdir($composer_dir);

        $input = new ArrayInput($this->options);

        $stream = fopen('php://temp', 'w+');
        $output = new StreamOutput($stream);
        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        $this->app->setAutoExit(false);
        $this->app->run($input, $output);

        rewind($stream);

        $stream = stream_get_contents($stream);

        $stream = substr($stream, strpos($stream, '{'));

        $this->parsePackages(json_decode($stream, true));

        return $this->packages;
    }

    protected function parsePackages($packages)
    {
        if ( ! isset($packages['installed'])) {
            return;
        }

        $this->packages = array_combine(
            array_column($packages['installed'], 'name'),
            $packages['installed']
        );
    }

    public function getPackages()
    {
        return $this->packages;
    }
}
