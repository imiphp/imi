<?php

declare(strict_types=1);

use Imi\App;
use Imi\Swoole\SwooleApp;

use function Swoole\Coroutine\run;

require \dirname(__DIR__, 3) . '/vendor/autoload.php';

run(static function (): void {
    App::runApp(__DIR__, SwooleApp::class, static function (): void {
        echo 'Test swoole quick start', \PHP_EOL;
    });
});
