<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use function array_merge;
use function explode;
use Imi\Test\BaseTest;
use Imi\Util\File;
use Imi\Util\Imi;
use function str_replace;
use Symfony\Component\Process\Process;
use function trim;

/**
 * @testdox Tool
 */
class ToolTest extends BaseTest
{
    public function testCoExit(): void
    {
        $cmd = [
            \PHP_BINARY,
            File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli'),
            '--app-namespace',
            'Imi\Test\Component',
            'TestTool/test',
        ];

        $process = new Process($cmd, \dirname(Imi::getNamespacePath('Imi')));
        $process->mustRun();

        $this->assertEquals(0, $process->getExitCode(), $process->getCommandLine());

        $cmd[] = '--code';
        $cmd[] = $code = mt_rand(0, 255);

        $process = new Process($cmd, \dirname(Imi::getNamespacePath('Imi')));
        $process->run();
        $this->assertEquals($code, $process->getExitCode(), $process->getCommandLine());
    }

    public function boolProvider(): \Generator
    {
        yield ['', [true, true, true, false, false, false]];
        yield ['--a1', [true, true, true, false, false, false]];
        yield ['-a', [true, true, true, false, false, false]];

        yield ['--b2', [true, true, true, true, true, true]];
        yield ['-b', [true, true, true, true, true, true]];

        yield [['--a1', '--b2'], [true, true, true, true, true, true]];
        yield [['-a', '-b'], [true, true, true, true, true, true]];

        yield [['--a1=0', '--b2=0'], [false, false, false, false, false, false]];
        yield [['-a0', '-b0'], [false, false, false, false, false, false]];
    }

    /**
     * @dataProvider boolProvider
     *
     * @param array|string $suffix
     */
    public function testBool($suffix, array $results): void
    {
        $cmd = [
            \PHP_BINARY,
            File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli'),
            '--app-namespace',
            'Imi\Test\Component',
            'TestTool/testBool',
        ];

        if (\is_array($suffix))
        {
            $cmd = array_merge($cmd, $suffix);
        }
        else
        {
            $cmd[] = $suffix;
        }
        $process = new Process($cmd, \dirname(Imi::getNamespacePath('Imi')));
        $process->mustRun();
        $this->assertEquals(0, $process->getExitCode());
        $output = $process->getOutput();
        $assertContent = [];
        foreach ($results as $result)
        {
            $assertContent[] = 'bool(' . var_export($result, true) . ')';
        }
        $this->assertEquals(
            $assertContent,
            \array_slice(explode("\n", str_replace("\r\n", "\n", trim($output))), -\count($results))
        );
    }

    public function argumentProvider(): \Generator
    {
        yield ['', ['', '', '']];
        yield ['imi', ['imi', 'imi', 'imi']];
    }

    /**
     * @dataProvider argumentProvider
     *
     * @param array|string $suffix
     */
    public function testArgument($suffix, array $results): void
    {
        $cmd = [
            \PHP_BINARY,
            File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli'),
            '--app-namespace',
            'Imi\Test\Component',
            'TestTool/testArgument',
        ];

        if (\is_array($suffix))
        {
            $cmd = array_merge($cmd, $suffix);
        }
        else
        {
            $cmd[] = $suffix;
        }
        $process = new Process($cmd, \dirname(Imi::getNamespacePath('Imi')));
        $process->mustRun();
        $this->assertEquals(0, $process->getExitCode());
        $output = $process->getOutput();

        $assertContent = [];
        foreach ($results as $result)
        {
            $assertContent[] = 'string(' . \strlen($result) . ') ' . json_encode($result, \JSON_UNESCAPED_UNICODE);
        }
        $this->assertEquals(
            $assertContent,
            \array_slice(explode("\n", str_replace("\r\n", "\n", trim($output))), -\count($results))
        );
    }

    public function negatableProvider(): \Generator
    {
        yield ['', [false, false]];
        yield ['--test', [true, true]];
        yield ['-t', [true, true]];
        yield ['--no-test', [false, false]];
    }

    /**
     * @dataProvider negatableProvider
     *
     * @param array|string $suffix
     */
    public function testNegatable($suffix, array $results): void
    {
        $cmd = [
            \PHP_BINARY,
            File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli'),
            '--app-namespace',
            'Imi\Test\Component',
            'TestTool/testNegatable',
        ];

        if (\is_array($suffix))
        {
            $cmd = array_merge($cmd, $suffix);
        }
        elseif ('' !== $suffix)
        {
            $cmd[] = $suffix;
        }
        $process = new Process($cmd, \dirname(Imi::getNamespacePath('Imi')));
        $process->mustRun();
        $this->assertEquals(0, $process->getExitCode());
        $output = $process->getOutput();

        $assertContent = [];
        foreach ($results as $result)
        {
            $assertContent[] = 'bool(' . var_export($result, true) . ')';
        }
        $this->assertEquals(
            $assertContent,
            \array_slice(explode("\n", str_replace("\r\n", "\n", trim($output))), -\count($results))
        );
    }
}
