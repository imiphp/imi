<?php

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Util\Imi;

/**
 * @testdox Tool
 */
class ToolTest extends BaseTest
{
    public function testCoExit()
    {
        if (version_compare(\SWOOLE_VERSION, '4.4', '>='))
        {
            $cmd = cmd('"' . \dirname(Imi::getNamespacePath('Imi')) . '/bin/imi" TestTool/test -appNamespace "Imi\Test\Component"');
            exec($cmd, $output, $exitCode);
            $this->assertEquals(0, $exitCode);

            $code = mt_rand(0, 255);
            exec($cmd . ' -code ' . $code, $output, $exitCode);
            $this->assertEquals($code, $exitCode);
        }
        else
        {
            $this->markTestSkipped('Swoole 4.3.x has exit bug');
        }
    }
}
