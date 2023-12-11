<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Contract\IEvent;
use Imi\Event\Contract\IEventDispatcher;

#[Bean(name: 'Event')]
class EventController
{
    public function __construct(protected object $target, protected ?IEventDispatcher $eventDispatcher = null)
    {
        $this->eventDispatcher ??= new EventDispatcher();
        if ($options = ClassEventManager::getObjectEvents($target))
        {
            foreach ($options as $classEvents)
            {
                foreach ($classEvents as $eventName => $eventOptions)
                {
                    foreach ($eventOptions as $listenerClass => $listenerOption)
                    {
                        // 数据映射
                        $this->addListener($eventName, static fn (IEvent $e) => App::newInstance($listenerClass)->handle($e), $listenerOption['priority']);
                    }
                }
            }
        }
    }

    public function getTarget(): object
    {
        return $this->target;
    }

    public function getEventDispatcher(): IEventDispatcher
    {
        return $this->eventDispatcher;
    }

    public function dispatch(IEvent $event): IEvent
    {
        return $this->eventDispatcher->dispatch($event);
    }

    public function addListener(string|array $eventNames, callable $listener, int $priority = 0, bool $once = false): void
    {
        $this->eventDispatcher->getListenerProvider()->addListener($eventNames, $listener, $priority, $once);
    }

    public function removeListener(string|array $eventNames, ?callable $listener = null): void
    {
        $this->eventDispatcher->getListenerProvider()->removeListener($eventNames, $listener);
    }
}
