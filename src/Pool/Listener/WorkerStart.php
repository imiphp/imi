<?php

declare(strict_types=1);

namespace Imi\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Core\CoreEvents;
use Imi\Event\Event;
use Imi\Event\IEventListener;
use Imi\Util\ImiPriority;

#[Listener(eventName: CoreEvents::APP_RUN, priority: \Imi\Util\ImiPriority::IMI_MAX, one: true)]
class WorkerStart implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Pool\Listener\RequestContextDestroy(), 'handle'], ImiPriority::IMI_MIN - 30);
    }
}
