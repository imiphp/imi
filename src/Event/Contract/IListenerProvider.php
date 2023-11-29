<?php

declare(strict_types=1);

namespace Imi\Event\Contract;

use Imi\Event\RegisteredListener;

interface IListenerProvider
{
    /**
     * @return iterable<RegisteredListener>
     */
    public function getListenersForEvent(IEvent $event): iterable;

    /**
     * @return iterable<string, RegisteredListener[]>
     */
    public function getListeners(): iterable;

    public function addListener(string|array $eventNames, callable $listener, int $priority = 0, bool $once = false): void;

    public function removeListener(string|array $eventNames, ?callable $listener = null): void;

    public function clearListeners(): void;
}
