<?php

declare(strict_types=1);

namespace Imi\Dev;

use FilesystemIterator;
use Imi\Cli\ImiCommand;
use function implode;
use function method_exists;
use function realpath;
use Symfony\Component\Process\Process;
use function usleep;

class Plugin
{
    public static function dev(): void
    {
        $componentsDir = \dirname(__DIR__) . '/src/Components';
        $output = ImiCommand::getOutput();
        $maxCount = 4;
        $running = 0;
        /** @var Process[] $readyProcesses */
        $readyProcesses = [];
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
            $process = self::createProcess($dir);
            $readyProcesses[$dir->getBasename()] = $process;
        }

        while (\count($readyProcesses))
        {
            foreach ($readyProcesses as $name => $process)
            {
                if (!$process->isStarted() && $maxCount > $running)
                {
                    ++$running;
                    $output->writeln("[Update <info>{$name}</info>]");
                    $process->start(function ($type, $buffer) {
                        echo $buffer;
                    });
                }
                elseif ($process->isStarted() && !$process->isRunning())
                {
                    --$running;
                    $result = $process->isSuccessful() ? '<info>success</info>' : "<error>fail({$process->getExitCode()})</error>";
                    $output->writeln("[Update <info>{$name}</info>]: {$result}");
                    unset($readyProcesses[$name]);
                }
            }
            usleep(1000 * 10);
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
