<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\Util\File;
use Imi\Util\Imi;
use Symfony\Component\Process\Process;

class FunctionTest extends BaseTest
{
    public function testDump(): void
    {
        $cmd = [
            \PHP_BINARY,
            File::path(\dirname(Imi::getNamespacePath('Imi')), 'src', 'Cli', 'bin', 'imi-cli'),
            '--app-namespace',
            'Imi\Test\Component',
            'TestTool/testDump',
        ];

        $process = new Process($cmd, \dirname(Imi::getNamespacePath('Imi')));
        $process->mustRun();
        $output = $process->getOutput();
        $this->assertTrue(str_contains($output, <<<'STR'
        imi.DEBUG: 
        string(10) "Hello imi!"
        STR));
    }
}
