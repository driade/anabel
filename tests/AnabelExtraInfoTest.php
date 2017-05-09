<?php

namespace Driade\Anabel\Test;

use Driade\Anabel\Anabel;
use Driade\Anabel\AnabelExtraInfo;

class AnabelExtraInfoTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function testOutdated()
    {
        $app_mock = \Mockery::mock('Composer\Console\Application')
            ->shouldReceive('setAutoExit')
            ->once()
            ->with(false)
            ->shouldReceive('run')
            ->once()
            ->with('Symfony\Component\Console\Input\ArrayInput', 'Symfony\Component\Console\Output\OutputInterface')
            ->getMock();

        $anabel = new AnabelExtraInfo;
        $anabel->setApp($app_mock);
        $output = $anabel->handle(array(), __DIR__);

        $this->assertSame(array(), $output);

        $this->assertSame(__DIR__, getcwd());
        $this->assertSame(array('command' => 'show', '-P' => '-P', '-f' => 'json'), $anabel->options);

        \Mockery::close();
    }

    /** @test */
    public function doNotParsesPackages()
    {
        $anabel           = new AnabelExtraInfo;
        $anabel->packages = array();
        $this->invoke($anabel, 'parsePackages', array(array('foo')));
        $this->assertEquals(array(), $anabel->packages);
    }

    /** @test */
    public function parsesPackagesAndGetHome()
    {
        $anabel = new AnabelExtraInfo;

        $anabel->packages = array(
            'foo' => array(
                'name'        => 'foo',
                'description' => 'acme',
            ),
        );

        $output = array(
            'foo' => array(
                'name'        => 'foo',
                'description' => 'acme',
                'homepage'    => 'home',
            ),
        );

        $this->invoke($anabel, 'parsePackages', array(array('installed' => array(array('name' => 'foo', 'path' => __DIR__ . '/example1/')))));

        $this->assertEquals($output, $anabel->packages);
    }

    public function parsesPackagesAndDontGetsHome()
    {
        $anabel = new AnabelExtraInfo;

        $anabel->packages = array(
            'foo' => array(
                'name'        => 'foo',
                'description' => 'acme',
            ),
        );

        $output = array(
            'foo' => array(
                'name'        => 'foo',
                'description' => 'acme',
            ),
        );

        $this->invoke($anabel, 'parsePackages', array(array('installed' => array(array('name' => 'foo', 'path' => __DIR__ . '/example-foo-missing/')))));

        $this->assertEquals($output, $anabel->packages);
    }

    public function invoke(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
