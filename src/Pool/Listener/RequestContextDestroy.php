<?php

declare(strict_types=1);

namespace Imi\Pool\Listener;

use Imi\Event\IEventListener;
use Imi\Pool\PoolManager;

class RequestContextDestroy implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        PoolManager::destroyCurrentContext();
    }
}
