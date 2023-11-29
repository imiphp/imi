<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\Event\Contract\IEvent;
use Imi\Event\Contract\IEventDispatcher;
use Imi\Event\Contract\IListenerProvider;

class EventDispatcher implements IEventDispatcher
{
    protected IListenerProvider $listenerProvider;

    public function __construct(?IListenerProvider $listenerProvider = null)
    {
        $this->listenerProvider = $listenerProvider ?? new ListenerProvider();
    }

    public function getListenerProvider(): IListenerProvider
    {
        return $this->listenerProvider;
    }

    public function dispatch(IEvent $event): IEvent
    {
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener)
        {
            /** @var IEvent $event */
            ($listener->listener)($event);
            if ($listener->once)
            {
                $this->listenerProvider->removeListener($eventName ??= $event->getEventName(), $listener->listener);
            }
            if ($event->isPropagationStopped())
            {
                break;
            }
        }

        return $event;
    }
}
