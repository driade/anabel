<?php

namespace Driade\Anabel\Test;

use Driade\Anabel\Anabel;

class AnabelTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function defaultConfig()
    {
        $anabel = new Anabel;

        $dir = __DIR__ . '/views';
        $dir = str_replace('/tests/', '/src/', $dir);

        $this->assertSame(
            array(
                'all'             => false,
                'composer_dir'    => '.',
                'templates_dir'   => $dir,
                'template_header' => 'header.twig.php',
                'template_body'   => 'body.twig.php',
                'template_footer' => 'footer.twig.php',
                'sort'            => true,
            )
            , $anabel->config);
    }

    /** @test */
    public function sortNotSort()
    {
        $anabel = new Anabel;
        $anabel->setConfig(array('sort' => false));
        $anabel->packages = array(array('foo'));

        $this->invoke($anabel, 'sort');

        $this->assertEquals(array(array('foo')), $anabel->packages);
    }

    /** @test */
    public function sortSorts()
    {
        $anabel = new Anabel;
        $anabel->setConfig(array('sort' => true));
        $anabel->packages = array(
            'foo1' => array(
                'name'   => 'foo',
                'status' => 'up-to-date',
            ),
            'foo2' => array(
                'name'   => 'foo2',
                'status' => 'up-to-date',
            ),
            'foo3' => array(
                'name'   => 'foo3',
                'status' => 'semver-safe-update',
            ),
            'foo4' => array(
                'name'   => 'foo4',
                'status' => 'update-possible',
            ),
        );

        $this->invoke($anabel, 'sort');

        $this->assertEquals(
            array(
                'foo3' => array(
                    'name'   => 'foo3',
                    'status' => 'semver-safe-update',
                ),

                'foo4' => array(
                    'name'   => 'foo4',
                    'status' => 'update-possible',
                ),
                'foo1' => array(
                    'name'   => 'foo',
                    'status' => 'up-to-date',
                ),
                'foo2' => array(
                    'name'   => 'foo2',
                    'status' => 'up-to-date',
                ),
            )
            , $anabel->packages);
    }

    /** @test */
    public function transformPackages()
    {
        $anabel           = new Anabel;
        $anabel->packages = array(
            'foo3' => array(
                'name'          => 'foo3',
                'latest-status' => 'semver-safe-update',
                'homepage'      => 'foo3-homepage',
                'version'       => 1,
                'latest'        => 1.1,
                'description'   => 'desc1',
            ),

            'foo4' => array(
                'name'          => 'foo4',
                'latest-status' => 'update-possible',
                'version'       => 2,
                'latest'        => 2.2,
                'description'   => 'desc2',
            ),
            'foo1' => array(
                'name'          => 'foo',
                'latest-status' => 'up-to-date',
                'version'       => 3,
                'latest'        => 3.3,
                'description'   => 'desc3',
            ),
            'foo2' => array(
                'name'          => 'foo2',
                'latest-status' => 'up-to-date',
                'homepage'      => 'foo2-homepage',
                'version'       => 4,
                'latest'        => 4.4,
                'description'   => 'desc4',
            ),
        );

        $this->invoke($anabel, 'transformPackages');

        $this->assertEquals(
            array(
                array(
                    'name'        => 'foo3',
                    'version'     => 1,
                    'latest'      => 1.1,
                    'status'      => 'semver-safe-update',
                    'description' => 'desc1',
                    'homepage'    => 'foo3-homepage',
                    'warning'     => '',
                ),
                array(
                    'name'        => 'foo4',
                    'version'     => 2,
                    'latest'      => 2.2,
                    'status'      => 'update-possible',
                    'description' => 'desc2',
                    'homepage'    => '',
                    'warning'     => '',
                ),
                array(
                    'name'        => 'foo',
                    'version'     => 3,
                    'latest'      => 3.3,
                    'status'      => 'up-to-date',
                    'description' => 'desc3',
                    'homepage'    => '',
                    'warning'     => '',
                ),
                array(
                    'name'        => 'foo2',
                    'version'     => 4,
                    'latest'      => 4.4,
                    'status'      => 'up-to-date',
                    'description' => 'desc4',
                    'homepage'    => 'foo2-homepage',
                    'warning'     => '',
                ),
            )
            , $anabel->packages);

    }

    /** @test */
    public function render()
    {
        $anabel = new Anabel;
        $anabel->setConfig(array(
            'templates_dir' => __DIR__ . '/example2/',
        ));
        $anabel->packages = array(
            array(
                'name'        => 'foo3',
                'version'     => 1,
                'latest'      => 1.1,
                'status'      => 'semver-safe-update',
                'description' => 'desc1',
                'homepage'    => 'foo3-homepage',
            ));

        $html = $anabel->render();

        $this->assertSame("header\nbody\nfooter\n", $html);
    }

    /** @test */
    public function setConfig()
    {
        $anabel = new Anabel;
        $anabel->setConfig(array(
            'foo' => 'foo',
        ));

        $this->assertEquals(array_merge($anabel->config, array('foo' => 'foo')), $anabel->config);
    }

    /** @test */
    public function getOption()
    {
        $anabel = new Anabel;
        $anabel->setConfig(array(
            'foo' => 'foo',
        ));

        $this->assertEquals('foo', $anabel->getOption('foo'));
    }

    public function invoke(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
