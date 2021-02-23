<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\Event\Event;
use Imi\Main\BaseMain;

class Main extends BaseMain
{
    public function __init()
    {
        Event::on('IMI.LOAD_RUNTIME', \Imi\Swoole\Process\Listener\LoadRuntimeListener::class, 19940000);
        Event::on('IMI.BUILD_RUNTIME', \Imi\Swoole\Process\Listener\BuildRuntimeListener::class, 19940000);

        Event::on('IMI.LOAD_RUNTIME', \Imi\Swoole\Task\Listener\LoadRuntimeListener::class, 19940000);
        Event::on('IMI.BUILD_RUNTIME', \Imi\Swoole\Task\Listener\BuildRuntimeListener::class, 19940000);
    }
}
