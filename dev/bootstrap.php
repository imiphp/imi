<?php

declare(strict_types=1);

use Imi\Config;
use Imi\Core\CoreEvents;
use Imi\Event\Event;
use Imi\Server\Event\ServerEvents;
use Imi\Util\Uri;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\PHP;

ini_set('date.timezone', 'Asia/Shanghai');

const TEST_APP_URI_CONFIG = [
    'host'     => 'imi-test',
    'port'     => 1234,
    'scheme'   => 'https',
    'user'     => 'root',
    'pass'     => '123',
    'path'     => '/test',
    'query'    => 'id=666',
    'fragment' => 'test',
];
const TEST_APP_URI = 'https://root:123@imi-test:1234/test?id=666#test';
function testAppCallbackUriConfig(Uri $uri): Uri
{
    return $uri->withHost('imi-test-callback')
                ->withPort(6666)
                ->withScheme('https')
                ->withUserInfo('admin', '123456')
                ->withPath('/testCallback')
                ->withQuery('id=999')
                ->withFragment('testCallback');
}
const TEST_APP_CALLBACK_URI = 'https://admin:123456@imi-test-callback:6666/testCallback?id=999#testCallback';

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
            \Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
            \Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector::class,
            \Rector\CodeQuality\Rector\ClassMethod\OptionalParametersAfterRequiredRector::class, // 调整包含默认值的参数顺序，会导致代码被破坏
            \Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector::class, // 常量自动加 final，无法继承覆盖了
            \Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector::class, // 自说自话改never真下头
        ]);

        $rectorConfig->bootstrapFiles([
            $path . '/vendor/autoload.php',
        ]);

        $rectorConfig->autoloadPaths([
            $path . '/src',
        ]);

        $rectorConfig->sets([LevelSetList::UP_TO_PHP_81]);

        $cacheDir = @getenv('RUNNING_CI_RECTOR_CACHE_DIR');

        if ($cacheDir && is_dir($cacheDir) && is_writable($cacheDir))
        {
            $rectorConfig->cacheClass(FileCacheStorage::class);
            $rectorConfig->cacheDirectory($cacheDir);
        }
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

    return $result . (\extension_loaded('xdebug') ? '' : ' -dzend_extension=xdebug') . ' -dxdebug.mode=coverage -dswoole.enable_fiber_mock';
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
        $result[] = '-dswoole.enable_fiber_mock';
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
    (static function (): void {
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
        $shutdownCallback = static function () use ($codeCoverage, &$stoped): void {
            if (!$stoped)
            {
                $stoped = true;
                $codeCoverage->stop();
                (new PHP())->process($codeCoverage, __DIR__ . '/cover/' . getenv('IMI_CODE_COVERAGE_NAME') . '/' . getmypid() . random_int(0, \PHP_INT_MAX) . '.clover.php');
            }
        };

        Event::on(ServerEvents::WORKER_STOP, $shutdownCallback);
        register_shutdown_function($shutdownCallback);
        Event::on(CoreEvents::LOAD_CONFIG, static function (): void {
            $config = [];
            if (!\extension_loaded('xdebug'))
            {
                $config[] = '-dzend_extension=xdebug';
            }
            $config[] = '-dxdebug.mode=coverage';
            $config[] = '-dswoole.enable_fiber_mock';
            Config::set('@app.imi.phpOptions', $config);
        });
    })();
}

function array_keys_string(array $array): array
{
    $keys = [];
    foreach ($array as $key => $_)
    {
        $keys[] = (string) $key;
    }

    return $keys;
}
