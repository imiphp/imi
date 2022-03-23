<?php

declare(strict_types=1);

namespace Imi\MQTT\Test;

use Imi\App;
use Imi\Swoole\SwooleApp;
use PHPUnit\Runner\BeforeFirstTestHook;

class PHPUnitHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        App::run('MQTTApp', SwooleApp::class, static function () {
        });
    }
}
