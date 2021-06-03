<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START")
 */
class SuperGlobals implements IWorkerStartEventListener
{
    public function handle(WorkerStartEventParam $e): void
    {
        /** @var \Imi\Server\Http\SuperGlobals\Listener\SuperGlobals $superGlobals */
        $superGlobals = App::getBean('SuperGlobals');
        $superGlobals->bind();
    }
}
