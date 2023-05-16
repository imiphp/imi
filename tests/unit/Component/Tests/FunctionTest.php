<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Util\File;
use Imi\Util\Imi;
use Symfony\Component\Process\Process;

class FunctionTest extends BaseTest
{
    public function testImiCallable(): void
    {
        $callable = imiCallable(static fn () => 1);
        $this->assertEquals(1, $callable());
    }

    public function testDump(): void
    {
        $cmd = [
            ...getTestPhpBinaryArray(),
            File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli'),
            '--app-namespace',
            'Imi\Test\Component',
            'TestTool/testDump',
        ];

        $process = new Process($cmd, \dirname(Imi::getNamespacePath('Imi')), [
            'IMI_CODE_COVERAGE_NAME' => getCodeCoverageName(),
        ]);
        $process->mustRun();
        $output = $process->getOutput();
        $this->assertTrue(false !== preg_match(<<<'STR'
        /imi\.DEBUG: [\r\n]+
        string(10) "Hello imi!"/
        STR, $output), 'output: ' . $output);
    }
}
