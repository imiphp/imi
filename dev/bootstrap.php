<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\PHP;

ini_set('date.timezone', 'Asia/Shanghai');

function getRectorConfigCallback(string $path): callable
{
    // @phpstan-ignore-next-line
    return static function (RectorConfig $rectorConfig) use ($path): void {
        // get parameters
        // @phpstan-ignore-next-line
        $rectorConfig->paths([
            $path . '/src',
        ]);

        $rectorConfig->skip([
            '*/vendor/*',
            $path . '/src/Components/*',
            \Rector\Php71\Rector\FuncCall\CountOnNullRector::class,
            \Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
            \Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
            \Rector\Php70\Rector\FuncCall\RandomFunctionRector::class,
        ]);

        $rectorConfig->bootstrapFiles([
            $path . '/vendor/autoload.php',
        ]);

        $rectorConfig->autoloadPaths([
            $path . '/src',
        ]);

        $rectorConfig->sets([LevelSetList::UP_TO_PHP_74]);
    };
}

function isCodeCoverage(): bool
{
    return (bool) (getenv('IMI_CODE_COVERAGE') ?: false);
}

function getCodeCoverageName(): string
{
    static $name;
    if (null === $name)
    {
        $name = getenv('IMI_CODE_COVERAGE_NAME');
        if (false === $name)
        {
            $name = (string) getmypid();
        }
    }

    return $name;
}

function registerCodeCoverage(): void
{
    if (!isCodeCoverage())
    {
        return;
    }

    $filter = new Filter();
    $filter->includeDirectory(\dirname(__DIR__) . '/src');
    $filter->excludeDirectory(\dirname(__DIR__) . '/src/Components');
    $componentsDir = \dirname(__DIR__) . '/src/Components';
    foreach (new \FilesystemIterator($componentsDir, \FilesystemIterator::SKIP_DOTS) as $dir)
    {
        if (!$dir->isDir())
        {
            continue;
        }
        $filter->includeDirectory($dir->getPathname() . '/src');
        // $filter->excludeDirectory($dir->getPathname() . '/vendor');
        // $filter->excludeDirectory($dir->getPathname() . '/test');
        // $filter->excludeDirectory($dir->getPathname() . '/tests');
        // $filter->excludeDirectory($dir->getPathname() . '/example');
    }

    $codeCoverage = new CodeCoverage((new Selector())->forLineCoverage($filter), $filter);
    $codeCoverage->start('imi');

    register_shutdown_function(static function () use ($codeCoverage) {
        $codeCoverage->stop();
        (new PHP())->process($codeCoverage, __DIR__ . '/cover/' . getenv('IMI_CODE_COVERAGE_NAME') . '/' . random_int(0, \PHP_INT_MAX) . '.clover.php');
    });
}

function getTestPhpBinary(): string
{
    $result = '"' . \PHP_BINARY . '"';
    if (!isCodeCoverage())
    {
        return $result;
    }

    return $result . ' -dzend_extension=xdebug -dxdebug.mode=coverage';
}

/**
 * @return string[]
 */
function getTestPhpBinaryArray(): array
{
    $result = [
        \PHP_BINARY,
    ];
    if (isCodeCoverage())
    {
        $result[] = '-dzend_extension=xdebug';
        $result[] = '-dxdebug.mode=coverage';
    }

    return $result;
}

if (isCodeCoverage())
{
    putenv('IMI_CODE_COVERAGE_NAME=' . getCodeCoverageName());
}
