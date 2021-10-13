<?php

declare(strict_types=1);

namespace Imi\Event\Listener;

use Imi\Config;
use Imi\Event\ClassEventManager;
use Imi\Event\EventManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        if (!Config::get('@app.imi.runtime.event', true))
        {
            return;
        }
        $eventData = $e->getData();
        $data = [];
        $data['event'] = EventManager::getMap();
        $data['classEvent'] = ClassEventManager::getMap();
        $eventData['data']['event'] = $data;
    }
}
