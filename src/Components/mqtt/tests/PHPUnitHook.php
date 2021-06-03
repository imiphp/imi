<?php

declare(strict_types=1);

namespace Imi\MQTT\Test;

use Imi\App;
use PHPUnit\Runner\BeforeFirstTestHook;

class PHPUnitHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        App::run('MQTTApp', TestApp::class);
    }
}
