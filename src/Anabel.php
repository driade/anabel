<?php

namespace Driade\Anabel;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Anabel
{
    protected $packages = [];
    protected $config   = [
        'all'             => false,
        'composer_dir'    => '.',
        'templates_dir'   => __DIR__ . '/views',
        'template_header' => 'header.twig.php',
        'template_body'   => 'body.twig.php',
        'template_footer' => 'footer.twig.php',
        'sort'            => true,
    ];

    public function outdated()
    {
        $this->getOutdated()
            ->findExtrainfo()
            ->transformPackages()
            ->sort();

        return $this;
    }

    protected function getOutdated()
    {
        $options = ['command' => 'outdated', '--format' => 'json'];

        if ($this->getOption('all')) {
            $options += ['--all' => '--all']; // FIX
        }

        chdir($this->getOption('composer_dir'));

        $input = new ArrayInput($options);

        $stream = fopen('php://temp', 'w+');
        $output = new StreamOutput($stream);
        $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        $app = new Application;
        $app->setAutoExit(false);
        $app->run(new ArrayInput($options), $output);

        rewind($stream);

        $stream = stream_get_contents($stream);

        // FIX
        $stream = substr($stream, strpos($stream, '{'));

        $packages = json_decode($stream, true);

        if (isset($packages['installed'])) {
            foreach ($packages['installed'] as $package) {
                $this->packages[$package['name']] = $package;
            }
        }

        return $this;
    }

    protected function findExtrainfo()
    {
        chdir($this->getOption('composer_dir'));

        $input = new ArrayInput(['command' => 'show', '-P' => '-P', '-f' => 'json']);

        $stream = fopen('php://temp', 'w+');

        $app = new Application;
        $app->setAutoExit(false);
        $app->run($input, new StreamOutput($stream));

        rewind($stream);

        $packages = json_decode(stream_get_contents($stream), true);

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

        return $this;
    }

    protected function sort()
    {
        if ($this->getOption('sort') === true) {
            uasort($this->packages, function ($a, $b) {
                return array_search($a['status'], ['semver-safe-update', 'update-possible', 'up-to-date']) > array_search($b['status'], ['semver-safe-update', 'update-possible', 'up-to-date']);
            });
        }

        return $this;
    }

    protected function transformPackages()
    {
        $packages = [];

        foreach ($this->packages as $package) {

            $homepage = '';

            if (isset($package['homepage'])) {
                $homepage = $package['homepage'];
            }

            $packages[] = [
                'name'        => $package['name'],
                'version'     => $package['version'],
                'latest'      => $package['latest'],
                'status'      => $package['latest-status'],
                'description' => $package['description'],
                'homepage'    => $homepage,
            ];
        }

        $this->packages = $packages;

        return $this;
    }

    public function render()
    {
        $loader = new Twig_Loader_Filesystem($this->getOption('templates_dir'));
        $twig   = new Twig_Environment($loader);

        $html = $twig->render($this->getOption('template_header'));
        $html .= $twig->render($this->getOption('template_body'), ['packages' => $this->packages]);
        $html .= $twig->render($this->getOption('template_footer'));

        return $html;
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    protected function getOption($key)
    {
        return $this->config[$key];
    }
}
