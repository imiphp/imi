<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Imi\App;
use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Tool\Tool;

Event::on('IMI.INITED', function(EventParam $param){
    $param->stopPropagation();
    Tool::init();
    echo 'imi inited!', PHP_EOL;
}, PHP_INT_MAX);

echo 'init imi...', PHP_EOL;

App::run('Imi\Test');
