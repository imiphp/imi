<?php

use Imi\App;
use Imi\Event\EventParam;

$loader = require dirname(__DIR__) . '/vendor/autoload.php';

\Swoole\Runtime::enableCoroutine();

ini_set('date.timezone', date_default_timezone_get());

\Imi\Event\Event::on('IMI.INIT_TOOL', function (EventParam $param) {
    $data = $param->getData();
    $data['skip'] = true;
    \Imi\Tool\Tool::init();
});
\Imi\Event\Event::on('IMI.INITED', function (EventParam $param) {
    App::initWorker();
    $param->stopPropagation();
}, 1);
App::run('QueueApp');
