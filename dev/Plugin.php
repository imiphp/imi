<?php

declare(strict_types=1);

namespace Imi\Dev;

use FilesystemIterator;
use Symfony\Component\Console\Output\ConsoleOutput;

class Plugin
{
    public static function dev(): void
    {
        $componentsDir = \dirname(__DIR__) . '/src/Components';
        $cmd = '"' . \PHP_BINARY . '" "' . realpath($_SERVER['SCRIPT_FILENAME']) . '" update';
        $output = new ConsoleOutput();
        foreach (new FilesystemIterator($componentsDir, FilesystemIterator::SKIP_DOTS) as $dir)
        {
            if (!$dir->isDir())
            {
                continue;
            }
            $fileName = $dir->getPathname() . '/composer.json';
            if (is_file($fileName))
            {
                $output->writeln('[Update <info>' . $dir->getBasename() . '</info>]');
                passthru('cd "' . $dir->getPathname() . '" && ' . $cmd);
            }
        }
    }
}
