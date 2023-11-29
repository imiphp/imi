<?php

declare(strict_types=1);

namespace Imi\Event\Listener;

use Imi\Config;
use Imi\Event\ClassEventManager;
use Imi\Event\EventManager;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\BuildRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.event', true))
        {
            return;
        }
        $data = [];
        $data['event'] = EventManager::getMap();
        $data['classEvent'] = ClassEventManager::getMap();
        $e->data['event'] = $data;
    }
}
