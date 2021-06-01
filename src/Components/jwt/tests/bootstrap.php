<?php

$loader = require dirname(__DIR__) . '/vendor/autoload.php';

use Imi\App;

\Swoole\Runtime::enableCoroutine();

register_shutdown_function(function () {
    App::getBean('Logger')->save();
});
