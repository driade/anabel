<?php

namespace Driade\Anabel;

use Composer\Console\Application;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Anabel
{
    public $packages = array();
    public $config   = array(
        'all'             => false,
        'composer_dir'    => '.',
        'templates_dir'   => '',
        'template_header' => 'header.twig.php',
        'template_body'   => 'body.twig.php',
        'template_footer' => 'footer.twig.php',
        'sort'            => true,
    );

    public function __construct()
    {
        $this->config['templates_dir'] = __DIR__ . '/views';
    }

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
        $outdated = new AnabelOutdated();
        $outdated->setApp(new Application);

        $this->packages = $outdated->handle($this->getOption('all'), $this->getOption('composer_dir'));

        return $this;
    }

    protected function findExtrainfo()
    {
        $extraInfo = new AnabelExtraInfo;
        $extraInfo->setApp(new Application);

        $this->packages = $extraInfo->handle($this->packages, $this->getOption('composer_dir'));

        return $this;
    }

    protected function sort()
    {
        if ($this->getOption('sort') === true) {
            uasort($this->packages, function ($a, $b) {
                return array_search($a['status'], array('semver-safe-update', 'update-possible', 'up-to-date')) > array_search($b['status'], array('semver-safe-update', 'update-possible', 'up-to-date'));
            });
        }

        return $this;
    }

    protected function transformPackages()
    {
        $packages = array();

        foreach ($this->packages as $package) {

            $homepage = '';

            if (isset($package['homepage'])) {
                $homepage = $package['homepage'];
            }

            $warning = '';

            if (isset($package['warning'])) {
                $warning = $package['warning'];
            }

            $packages[] = array(
                'name'        => $package['name'],
                'version'     => $package['version'],
                'latest'      => $package['latest'],
                'status'      => $package['latest-status'],
                'description' => $package['description'],
                'homepage'    => $homepage,
                'warning'     => $warning,
            );
        }

        $this->packages = $packages;

        return $this;
    }

    public function render()
    {
        $loader = new Twig_Loader_Filesystem($this->getOption('templates_dir'));
        $twig   = new Twig_Environment($loader);

        $html = $twig->render($this->getOption('template_header'));
        $html .= $twig->render($this->getOption('template_body'), array('packages' => $this->packages));
        $html .= $twig->render($this->getOption('template_footer'));

        return $html;
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function getOption($key)
    {
        return $this->config[$key];
    }
}
