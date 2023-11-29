<?php

declare(strict_types=1);

namespace Imi\Event\Contract;

interface IEventDispatcher
{
    public function dispatch(IEvent $event): IEvent;

    public function getListenerProvider(): IListenerProvider;
}
