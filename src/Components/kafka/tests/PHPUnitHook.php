<?php

declare(strict_types=1);

namespace Imi\Kafka\Test;

use Imi\App;
use Imi\Swoole\SwooleApp;
use PHPUnit\Runner\BeforeFirstTestHook;

class PHPUnitHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        App::run('KafkaApp', SwooleApp::class, static function () {
        });
    }
}
