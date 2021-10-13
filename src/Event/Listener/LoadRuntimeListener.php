<?php

declare(strict_types=1);

namespace Imi\Event\Listener;

use Imi\Config;
use Imi\Event\ClassEventManager;
use Imi\Event\EventManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

class LoadRuntimeListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $config = Config::get('@app.imi.runtime', []);
        if (!($config['event'] ?? true))
        {
            return;
        }
        $data = $e->getData()['data']['event'] ?? [];
        EventManager::setMap($data['event'] ?? []);
        ClassEventManager::setMap($data['classEvent'] ?? []);
    }
}
