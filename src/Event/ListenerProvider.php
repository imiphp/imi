<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\Event\Contract\IEvent;
use Imi\Event\Contract\IListenerProvider;

class ListenerProvider implements IListenerProvider
{
    /**
     * @var array<string, RegisteredListener[]>
     */
    protected array $listeners = [];

    /**
     * @var array<string, RegisteredListener[]>
     */
    protected array $sortedListeners = [];

    /**
     * @var array<string, bool>
     */
    protected array $changedEventMap = [];

    /**
     * @return iterable<RegisteredListener>
     */
    public function getListenersForEvent(IEvent $event): iterable
    {
        $eventName = $event->getEventName();
        if (isset($this->changedEventMap[$eventName]))
        {
            $this->rebuildSortedListeners($eventName);
            unset($this->changedEventMap[$eventName]);
        }

        return $this->sortedListeners[$eventName] ?? [];
    }

    /**
     * @return iterable<string, RegisteredListener[]>
     */
    public function getListeners(): iterable
    {
        if ($this->changedEventMap)
        {
            foreach ($this->changedEventMap as $eventName => $_)
            {
                $this->rebuildSortedListeners($eventName);
            }
            $this->changedEventMap = [];
        }

        return $this->sortedListeners;
    }

    public function addListener(string|array $eventNames, callable $listener, int $priority = 0, bool $once = false): void
    {
        foreach ((array) $eventNames as $eventName)
        {
            $this->listeners[$eventName][] = new RegisteredListener($listener, $priority, $once);
            unset($this->sortedListeners[$eventName]);
            $this->changedEventMap[$eventName] = true;
        }
    }

    public function removeListener(string|array $eventNames, ?callable $listener = null): void
    {
        foreach ((array) $eventNames as $eventName)
        {
            if (null === $listener)
            {
                unset($this->listeners[$eventName]);
            }
            elseif (isset($this->listeners[$eventName]))
            {
                foreach ($this->listeners[$eventName] as $i => $registeredListener)
                {
                    if ($registeredListener->listener === $listener)
                    {
                        unset($this->listeners[$eventName][$i]);
                    }
                }
                if (!$this->listeners[$eventName])
                {
                    unset($this->listeners[$eventName]);
                }
            }
            unset($this->sortedListeners[$eventName]);
            $this->changedEventMap[$eventName] = true;
        }
    }

    public function clearListeners(): void
    {
        $this->listeners = $this->sortedListeners = $this->changedEventMap = [];
    }

    protected function rebuildSortedListeners(string $eventName): void
    {
        if (isset($this->listeners[$eventName]))
        {
            $this->sortedListeners[$eventName] = $this->listeners[$eventName];
            // priority 越大越靠前
            // priority 相等时，createTime 越早越靠前
            usort($this->sortedListeners[$eventName], static fn (RegisteredListener $a, RegisteredListener $b): int => ($a->priority === $b->priority ? (int) (($a->createTime - $b->createTime) * 1000000) : (int) ($b->priority - $a->priority)));
        }
        else
        {
            unset($this->sortedListeners[$eventName]);
        }
    }
}
