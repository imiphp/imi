<?php

declare(strict_types=1);

namespace Imi\Cli\Listener;

use Imi\Cli\CliManager;
use Imi\Config;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.cli', true))
        {
            return;
        }
        $eventData = $e->getData();
        $data = [];
        $data['cli'] = CliManager::getMap();
        $eventData['data']['cli'] = $data;
    }
}
