<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\Event\Contract\IEvent;

/**
 * 不区分事件名大小写的事件监听提供者.
 *
 * 仅作为 3.0 兼容 2.1 用途，将在 3.1 废弃
 *
 * @deprecated 3.1
 */
class CaseInsensitiveListenerProvider extends ListenerProvider
{
    /**
     * @return iterable<RegisteredListener>
     */
    public function getListenersForEvent(IEvent $event): iterable
    {
        $eventName = strtolower($event->getEventName());
        if (isset($this->changedEventMap[$eventName]))
        {
            $this->rebuildSortedListeners($eventName);
            unset($this->changedEventMap[$eventName]);
        }

        return $this->sortedListeners[$eventName] ?? [];
    }

    public function addListener(string|array $eventNames, callable $listener, int $priority = 0, bool $once = false): void
    {
        $eventNames = (array) $eventNames;
        array_walk($eventNames, static function (string &$value): void {
            $value = strtolower($value);
        });
        parent::addListener($eventNames, $listener, $priority, $once);
    }

    public function removeListener(string|array $eventNames, ?callable $listener = null): void
    {
        $eventNames = (array) $eventNames;
        array_walk($eventNames, static function (string &$value): void {
            $value = strtolower($value);
        });
        parent::removeListener($eventNames, $listener);
    }
}
