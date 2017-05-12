<?php

namespace Driade\Anabel\Test;

use Driade\Anabel\Anabel;

class AnabelFullTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function complete()
    {
        $anabel = new Anabel;
        $anabel->setConfig(array(
            'all'          => true,
            'composer_dir' => __DIR__ . '/example3/',
            'sort'         => true,
        ));
        $anabel->outdated();

        $html = $anabel->render();

        $this->assertContains('psr/log', $html);
        $this->assertContains("<td class='text-right'>1.0.0</td>", $html);
    }
}
