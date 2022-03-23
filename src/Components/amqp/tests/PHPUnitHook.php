<?php

declare(strict_types=1);

namespace Imi\AMQP\Test;

use Imi\App;
use Imi\Cli\CliApp;
use PHPUnit\Runner\BeforeFirstTestHook;

class PHPUnitHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        App::run('AMQPApp', CliApp::class, static function () {
        });
    }
}
