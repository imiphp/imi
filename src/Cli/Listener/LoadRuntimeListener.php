<?php

declare(strict_types=1);

namespace Imi\Cli\Listener;

use Imi\Cli\CliManager;
use Imi\Config;
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
        if (!($config['cli'] ?? true))
        {
            return;
        }
        $data = $e->getData()['data']['cli'] ?? [];
        CliManager::setMap($data['cli'] ?? []);
    }
}
