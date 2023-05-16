<?php

declare(strict_types=1);

use Imi\Util\File;
use SebastianBergmann\CodeCoverage\Report\Clover;
use SebastianBergmann\CodeCoverage\Report\Html\Facade;

\define('ROOT_DIR', \dirname(__DIR__));

require ROOT_DIR . '/vendor/autoload.php';

/** @var \Imi\Util\File\FileEnumItem $file */
foreach (File::enumFile(ROOT_DIR . '/dev/cover') as $file)
{
    if (is_dir($path = $file->getFullPath()))
    {
        $file->setContinue(false);
    }
    else
    {
        echo 'Loading coverage ', $path, '...', \PHP_EOL;
        /** @var \SebastianBergmann\CodeCoverage\CodeCoverage $codeCoverage */
        $tmpCodeCoverage = include $path;
        if (isset($codeCoverage))
        {
            $codeCoverage->merge($tmpCodeCoverage);
        }
        else
        {
            $codeCoverage = $tmpCodeCoverage;
        }
    }
}

$pids = explode(',', $_SERVER['argv'][1]);

foreach ($pids as $pid)
{
    echo 'Loading coverage ', $pid, '...', \PHP_EOL;
    foreach (File::enum(ROOT_DIR . '/dev/cover/' . $pid) as $file)
    {
        echo 'Loading coverage ', $file, '...', \PHP_EOL;
        $data = include $file;
        $codeCoverage->merge($data);
    }
}

echo 'Generating coverage report...', \PHP_EOL;

if ('clover' === ($_SERVER['argv'][2] ?? 'html'))
{
    // clover
    (new Clover())->process($codeCoverage, ROOT_DIR . '/tests/core-coverage.xml');
}
else
{
    // html
    (new Facade())->process($codeCoverage, ROOT_DIR . '/tests/html-coverage');
}
