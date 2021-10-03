<?php

declare(strict_types=1);

namespace Imi\Dev;

use FilesystemIterator;
use Imi\Cli\ImiCommand;
use Swoole\Coroutine;
use function count;
use function imiGetEnv;
use function implode;
use function method_exists;
use Swoole\Event;
use Swoole\Runtime;
use Symfony\Component\Process\Process;
use function realpath;
use function usleep;

class Plugin
{
    public static function dev(): void
    {
        $componentsDir = \dirname(__DIR__) . '/src/Components';
        $output = ImiCommand::getOutput();
        $maxCount = 6;
        /** @var Process[] $readyProcesses */
        $readyProcesses = [];
        /** @var Process[] $activeProcesses */
        $activeProcesses = [];
        foreach (new FilesystemIterator($componentsDir, FilesystemIterator::SKIP_DOTS) as $dir)
        {
            if (!$dir->isDir())
            {
                continue;
            }
            $fileName = $dir->getPathname() . '/composer.json';
            if (!is_file($fileName))
            {
                continue;
            }
            $output->writeln("[Update <info>{$dir->getBasename()}</info>]");
            $process = self::createProcess($dir);
            $readyProcesses[$dir->getBasename()] = $process;
        }

        while (count($readyProcesses) || count($activeProcesses)) {
            foreach ($activeProcesses as $name => $process) {
                if (!$process->isRunning()) {
                    $result = $process->isSuccessful() ? '<info>success</info>' : "<error>fail({$process->getExitCode()})</error>";
                    $output->writeln("[Update <info>{$name}</info>]: {$result}");
                    unset($activeProcesses[$name]);
                }

                // check every second
                usleep(1000 * 10);
            }
            foreach ($readyProcesses as $name => $process) {
                if (count($activeProcesses) >= $maxCount) {
                    break;
                }
                unset($readyProcesses[$name]);
                $process->start(function ($type, $buffer) {
                    echo $buffer;
                });
                $activeProcesses[$name] = $process;
            }
        }
    }

    protected static function createProcess(\SplFileInfo $dir): Process
    {
        $cmd = [
            \PHP_BINARY,
            realpath($_SERVER['SCRIPT_FILENAME']),
            'update',
            '--no-interaction',
            '--prefer-dist',
            '--no-progress',
        ];
        // 兼容 symfony process < 3.3
        if (method_exists(Process::class, 'fromShellCommandline'))
        {
            $p = new Process($cmd, $dir->getPathname(), null, null, 0);
        }
        else
        {
            $p = new Process([], $dir->getPathname(), null, null, 0);
            /* @phpstan-ignore-next-line */
            $p->setCommandLine(implode(' ', $cmd));
        }
        return $p;
    }
}
