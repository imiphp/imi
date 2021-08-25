<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Util\File;
use Imi\Util\Imi;

/**
 * @testdox Tool
 */
class ToolTest extends BaseTest
{
    public function testCoExit(): void
    {
        $cmd = \Imi\cmd('"' . \PHP_BINARY . '" "' . File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli"') . ' TestTool/test --app-namespace "Imi\Test\Component"');
        exec($cmd, $output, $exitCode);
        $this->assertEquals(0, $exitCode);

        $code = mt_rand(0, 255);
        exec($cmd . ' --code ' . $code, $output, $exitCode);
        $this->assertEquals($code, $exitCode);
    }

    public function testBool(): void
    {
        $cmd = \Imi\cmd('"' . \PHP_BINARY . '" "' . File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli"') . ' TestTool/testBool --app-namespace "Imi\Test\Component"');
        exec($cmd, $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(false)', array_pop($output));
        $this->assertEquals('bool(true)', array_pop($output));

        exec($cmd . ' --a1', $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(false)', array_pop($output));
        $this->assertEquals('bool(true)', array_pop($output));
        exec($cmd . ' -a', $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(false)', array_pop($output));
        $this->assertEquals('bool(true)', array_pop($output));

        exec($cmd . ' --b2', $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(true)', array_pop($output));
        $this->assertEquals('bool(true)', array_pop($output));
        exec($cmd . ' -b', $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(true)', array_pop($output));
        $this->assertEquals('bool(true)', array_pop($output));

        exec($cmd . ' --a1 --b2', $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(true)', array_pop($output));
        $this->assertEquals('bool(true)', array_pop($output));
        exec($cmd . ' -a -b', $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(true)', array_pop($output));
        $this->assertEquals('bool(true)', array_pop($output));

        exec($cmd . ' --a1=0 --b2=0', $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(false)', array_pop($output));
        $this->assertEquals('bool(false)', array_pop($output));

        exec($cmd . ' -a0 -b0', $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertEquals('bool(false)', array_pop($output));
        $this->assertEquals('bool(false)', array_pop($output));
    }
}
