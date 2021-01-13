<?php

$loader = require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';

use Imi\App;

App::setLoader($loader);
if (defined('SWOOLE_HOOK_NATIVE_CURL'))
{
    // 暂时移除 PHP 8 中有 bug 的 hook native curl
    $flags = \SWOOLE_HOOK_ALL ^ \SWOOLE_HOOK_NATIVE_CURL;
}
else
{
    $flags = \SWOOLE_HOOK_ALL;
}
\Swoole\Runtime::enableCoroutine($flags);

register_shutdown_function(function () {
    App::getBean('Logger')->save();
});
