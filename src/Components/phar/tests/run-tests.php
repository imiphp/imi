<?php

declare(strict_types=1);

use Imi\RoadRunner\Util\RoadRunner;
use Symfony\Component\Process\Process;

require \dirname(__DIR__, 2) . '/roadrunner/vendor/autoload.php';
require \dirname(__DIR__) . '/vendor/autoload.php';

const STARTUP_MAX_WAIT = 30;

const LOCAL_REPOSITORIES = [
    'imiphp/imi'            => '',
    'imiphp/imi-macro'      => 'src/Components/macro',
    'imiphp/imi-swoole'     => 'src/Components/swoole',
    'imiphp/imi-workerman'  => 'src/Components/workerman',
    'imiphp/imi-roadrunner' => 'src/Components/roadrunner',
    'imiphp/imi-phar'       => 'src/Components/phar',
];

$srcSourceDir = \dirname(__DIR__, 4);
$srcMirrorDir = '/tmp/mirror-imi';
$testProjectSrc = __DIR__ . '/project';
$testProjectDir = '/tmp/imi-phar-test';

echo '> Copy files...', \PHP_EOL;
$rsyncImiSrc = <<<SHELL
rsync -av \
 --exclude '.git' --exclude '.idea' --exclude '*.log' \
 --exclude '.runtime' --exclude '*/.runtime' \
 --exclude 'dev' --exclude 'doc' --exclude 'mddoc' \
 --exclude 'composer.lock' --exclude 'src/Components/*/composer.lock' \
 --exclude 'vendor' --exclude 'src/Components/*/vendor' \
 --exclude 'tests' --exclude 'src/Components/*/tests' \
 --delete {$srcSourceDir}/ {$srcMirrorDir}
SHELL;
Process::fromShellCommandline($rsyncImiSrc)->mustRun();
Process::fromShellCommandline("rm -r {$testProjectDir}")->run();
Process::fromShellCommandline("cp -r {$testProjectSrc} {$testProjectDir}")->mustRun();

echo '> Generate config...', \PHP_EOL;
$composerJson = json_decode(file_get_contents($testProjectSrc . '/composer.json'), true, \JSON_THROW_ON_ERROR);

foreach (LOCAL_REPOSITORIES as $package => $path)
{
    echo "Set package: {$package} => {$path}", \PHP_EOL;
    $composerJson['repositories'][] = [
        'type'    => 'path',
        'url'     => $path ? ($srcMirrorDir . \DIRECTORY_SEPARATOR . $path) : $srcMirrorDir,
        'options' => [
            'symlink'  => false,
            'versions' => [
                $package => '2.1.9999',
            ],
        ],
    ];
}

$newJson = json_encode($composerJson, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_PRETTY_PRINT);
file_put_contents($testProjectDir . '/composer.json', $newJson);

echo '> Composer install...', \PHP_EOL;
(new Process([
    'composer',
    'install',
    '--no-interaction',
    '--prefer-dist',
    '--no-progress',
], $testProjectDir, [
    //     'COMPOSER_DISABLE_NETWORK' => '1', // 本地测试提升速度用
]))
    ->mustRun(static function ($type, $buffer) {
        echo $buffer;
    });

$testContainer = [];
if (\extension_loaded('swoole'))
{
    $testContainer['swoole'] = ['build/imi.phar', 'swoole/start'];
}
$testContainer['workerman'] = ['build/imi.phar', 'workerman/start'];
if (null !== RoadRunner::getBinaryPath())
{
    $testContainer['roadrunner'] = ['build/imi-cli.phar', 'rr/start']; // 两个入口 roadrunner、cli
}

foreach ($testContainer as $container => $opt)
{
    echo "> Build {$container} phar...", \PHP_EOL;

    [$entrance, $cmd] = $opt;

    (new Process([
        \PHP_BINARY,
        'vendor/bin/imi-phar',
        'build', $container,
        '--no-interaction',
        '--no-ansi',
    ], $testProjectDir))
        ->mustRun(static function ($type, $buffer) {
            echo $buffer;
        });

    if ('roadrunner' === $container)
    {
        (new Process([
            \PHP_BINARY,
            'vendor/bin/imi-phar',
            'build', 'cli',
            '-o', 'build/imi-cli.phar',
            '--no-interaction',
            '--no-ansi',
        ], $testProjectDir))
            ->mustRun(static function ($type, $buffer) {
                echo $buffer;
            });
    }

    echo "> Run {$container} phar...", \PHP_EOL;
    $testServer = (new Process([
        \PHP_BINARY,
        $entrance,
        $cmd,
        '--no-interaction',
        '--no-ansi',
    ], $testProjectDir));

    $testServer->start(static function ($type, $buffer) {
        echo $buffer;
    });
    try
    {
        echo '> Wait running', \PHP_EOL;
        $context = stream_context_create(['http' => ['timeout' => 3]]);
        $count = 0;
        $testSuccess = false;
        while ($testServer->isRunning() && $count++ < STARTUP_MAX_WAIT)
        {
            try
            {
                $text = @file_get_contents('http://127.0.0.1:13000/', false, $context);
                if ('imi' === $text)
                {
                    $testSuccess = true;
                    echo \PHP_EOL, 'response success';
                    break;
                }
            }
            catch (ErrorException $e)
            {
            }
            sleep(1);
        }
        echo \PHP_EOL;

        if ($testSuccess && (!is_file($testProjectDir . '/build/.env') || !is_file($testProjectDir . '/build/.env.bak') || !is_file($testProjectDir . '/build/resources/a.txt')))
        {
            echo 'Not found a.txt',\PHP_EOL;
            $testSuccess = false;
        }
    }
    finally
    {
        $testServer->stop();
        if (!$testSuccess)
        {
            echo "> Test {$container} phar fail!!!";
            exit(1);
        }
    }
}
