<?php

namespace Driade\Anabel;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class AnabelExtraInfo
{
    public function setApp(Application $app)
    {
        $this->app = $app;
    }

    public function handle(array $packages, $composer_dir)
    {
        $this->packages = $packages;
        $this->options  = array('command' => 'show', '-P' => '-P', '-f' => 'json');

        chdir($composer_dir);

        $input = new ArrayInput($this->options);

        $stream = fopen('php://temp', 'w+');

        $this->app->setAutoExit(false);
        $this->app->run($input, new StreamOutput($stream));

        rewind($stream);

        $stream = stream_get_contents($stream);

        $stream = substr($stream, strpos($stream, '{'));

        $this->parsePackages(json_decode($stream, true));

        return $this->packages;
    }

    protected function parsePackages($packages)
    {
        if (isset($packages['installed'])) {

            foreach ($packages['installed'] as $package) {

                if (isset($package['name'], $package['path'], $this->packages[$package['name']])) {

                    if (file_exists($package['path'] . '/composer.json')) {

                        $composer = file_get_contents($package['path'] . '/composer.json');
                        $composer = json_decode($composer, true);

                        if (isset($composer['homepage'])) {
                            $this->packages[$package['name']]['homepage'] = $composer['homepage'];
                        }
                    }
                }
            }
        }
    }
}
