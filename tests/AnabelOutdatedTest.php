<?php

namespace Driade\Anabel\Test;

use Driade\Anabel\Anabel;
use Driade\Anabel\AnabelOutdated;

class AnabelOutdatedTest extends \PHPUnit_Framework_TestCase
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

        $anabel = new AnabelOutdated();
        $anabel->setApp($app_mock);
        $output = $anabel->handle(false, __DIR__);

        $this->assertSame(array(), $output);

        $this->assertSame(__DIR__, getcwd());
        $this->assertSame(array('command' => 'outdated', '--format' => 'json'), $anabel->options);

        \Mockery::close();
    }

    /** @test */
    public function doNotParsesPackages()
    {
        $anabel = new AnabelOutdated;
        $this->invoke($anabel, 'parsePackages', array(array('foo')));

        $this->assertEquals(array(), $anabel->getPackages());
    }

    /** @test */
    public function parsesPackages()
    {
        $anabel = new AnabelOutdated;

        $output = array(
            'foo' => array(
                'name'        => 'foo',
                'description' => 'acme',
            ),
        );
        $this->invoke($anabel, 'parsePackages', array(array('installed' => array(array('name' => 'foo', 'description' => 'acme')))));

        $this->assertEquals($output, $anabel->getPackages());
    }

    public function invoke(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
