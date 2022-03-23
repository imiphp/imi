<?php

declare(strict_types=1);

namespace Imi\Queue\Test;

use Imi\App;
use Imi\Swoole\SwooleApp;
use PHPUnit\Runner\BeforeFirstTestHook;

class PHPUnitHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        App::run('QueueApp', SwooleApp::class, static function () {
        });
    }
}
