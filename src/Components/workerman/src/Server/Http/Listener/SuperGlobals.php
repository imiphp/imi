<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Workerman\Event\WorkermanEvents;

#[Listener(eventName: WorkermanEvents::SERVER_WORKER_START, one: true)]
class SuperGlobals implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        /** @var \Imi\Server\Http\SuperGlobals\Listener\SuperGlobals $superGlobals */
        $superGlobals = App::getBean('SuperGlobals');
        $superGlobals->bind();
    }
}
