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

        $assert = function (string $suffix, array $results) use ($cmd) {
            exec($cmd . $suffix, $output, $exitCode);
            $this->assertEquals(0, $exitCode);
            $assertContent = [];
            foreach ($results as $result)
            {
                $assertContent[] = 'bool(' . var_export($result, true) . ')';
            }
            $this->assertEquals($assertContent, \array_slice($output, -\count($results)));
        };
        $assert('', [true, true, true, false, false, false]);
        $assert(' --a1', [true, true, true, false, false, false]);
        $assert(' -a', [true, true, true, false, false, false]);

        $assert(' --b2', [true, true, true, true, true, true]);
        $assert(' -b', [true, true, true, true, true, true]);

        $assert(' --a1 --b2', [true, true, true, true, true, true]);
        $assert(' -a -b', [true, true, true, true, true, true]);

        $assert(' --a1=0 --b2=0', [false, false, false, false, false, false]);
        $assert(' -a0 -b0', [false, false, false, false, false, false]);
    }

    public function testArgument(): void
    {
        $cmd = \Imi\cmd('"' . \PHP_BINARY . '" "' . File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli"') . ' TestTool/testArgument --app-namespace "Imi\Test\Component"');

        $assert = function (string $suffix, array $results) use ($cmd) {
            exec($cmd . $suffix, $output, $exitCode);
            $this->assertEquals(0, $exitCode);
            $assertContent = [];
            foreach ($results as $result)
            {
                $assertContent[] = 'string(' . \strlen($result) . ') ' . json_encode($result, \JSON_UNESCAPED_UNICODE);
            }
            $this->assertEquals($assertContent, \array_slice($output, -\count($results)));
        };

        $assert('', ['', '', '']);

        $assert(' imi', ['imi', 'imi', 'imi']);
    }

    public function testNegatable(): void
    {
        $cmd = \Imi\cmd('"' . \PHP_BINARY . '" "' . File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli"') . ' TestTool/testNegatable --app-namespace "Imi\Test\Component"');

        $assert = function (string $suffix, array $results) use ($cmd) {
            exec($cmd . $suffix, $output, $exitCode);
            $this->assertEquals(0, $exitCode);
            $assertContent = [];
            foreach ($results as $result)
            {
                $assertContent[] = 'bool(' . var_export($result, true) . ')';
            }
            $this->assertEquals($assertContent, \array_slice($output, -\count($results)));
        };

        $assert('', [false, false]);

        $assert(' --test', [true, true]);
        $assert(' -t', [true, true]);

        $assert(' --no-test', [false, false]);
    }
}
