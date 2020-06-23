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
        $cmd = dirname(Imi::getNamespacePath('Imi')) . '/bin/imi TestTool/test -appNamespace "Imi\Test\Component"';
        exec($cmd, $output, $exitCode);
        $this->assertEquals(0, $exitCode);

        if(version_compare(SWOOLE_VERSION, '4.4', '>='))
        {
            $code = mt_rand(0, 255);
            exec($cmd . ' -code ' . $code, $output, $exitCode);
            $this->assertEquals($code, $exitCode);
        }
    }

}
