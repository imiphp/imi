<?php

declare(strict_types=1);

use Imi\Event\Event;
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

function getTestPhpBinary(): string
{
    $result = '"' . \PHP_BINARY . '"';
    if (!isCodeCoverage())
    {
        return $result;
    }

    return $result . (\extension_loaded('xdebug') ? '' : ' -dzend_extension=xdebug') . ' -dxdebug.mode=coverage';
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
        if (!\extension_loaded('xdebug'))
        {
            $result[] = '-dzend_extension=xdebug';
        }
        $result[] = '-dxdebug.mode=coverage';
    }

    return $result;
}

/**
 * 检查端口是否可以被绑定.
 */
function checkPort(string $host, int $port, ?int &$errno = null, ?string &$errstr = null): bool
{
    try
    {
        $socket = @stream_socket_client('tcp://' . $host . ':' . $port, $errno, $errstr, 3);
        if (!$socket)
        {
            return false;
        }
        fclose($socket);

        return true;
    }
    catch (\Throwable $th)
    {
        return false;
    }
}

/**
 * 批量检查端口是否可以被绑定.
 */
function checkPorts(array $ports, string $host = '127.0.0.1', int $tryCount = 30, int $sleep = 1): void
{
    echo 'checking ports...', \PHP_EOL;
    foreach ($ports as $port)
    {
        echo "checking port {$port}...";
        $count = 0;
        while (checkPort($host, $port))
        {
            if ($count >= $tryCount)
            {
                echo 'failed', \PHP_EOL;
                continue 2;
            }
            ++$count;
            sleep($sleep);
        }
        echo 'OK', \PHP_EOL;
    }
}

if (isCodeCoverage())
{
    putenv('IMI_CODE_COVERAGE_NAME=' . getCodeCoverageName());
    (static function () {
        $filter = new Filter();
        $filter->includeDirectory(\dirname(__DIR__) . '/src');
        $componentsDir = \dirname(__DIR__) . '/src/Components';
        $filter->excludeDirectory($componentsDir);
        foreach (new \FilesystemIterator($componentsDir, \FilesystemIterator::SKIP_DOTS) as $dir)
        {
            if (!$dir->isDir())
            {
                continue;
            }
            $filter->includeDirectory($dir->getPathname() . '/src');
        }

        $codeCoverage = new CodeCoverage((new Selector())->forLineCoverage($filter), $filter);
        $codeCoverage->start('imi');

        $stoped = false;
        $shutdownCallback = static function () use ($codeCoverage, &$stoped) {
            if (!$stoped)
            {
                $stoped = true;
                $codeCoverage->stop();
                (new PHP())->process($codeCoverage, __DIR__ . '/cover/' . getenv('IMI_CODE_COVERAGE_NAME') . '/' . getmypid() . random_int(0, \PHP_INT_MAX) . '.clover.php');
            }
        };

        Event::on('IMI.SERVER.WORKER_STOP', $shutdownCallback);
        register_shutdown_function($shutdownCallback);
    })();
}
