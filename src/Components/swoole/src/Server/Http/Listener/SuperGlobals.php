<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;

#[Listener(eventName: 'IMI.MAIN_SERVER.WORKER.START', one: true)]
class SuperGlobals implements IWorkerStartEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(WorkerStartEventParam $e): void
    {
        /** @var \Imi\Server\Http\SuperGlobals\Listener\SuperGlobals $superGlobals */
        $superGlobals = App::getBean('SuperGlobals');
        $superGlobals->bind();
    }
}
