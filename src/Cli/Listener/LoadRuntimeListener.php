<?php

declare(strict_types=1);

namespace Imi\Cli\Listener;

use Imi\Cli\CliManager;
use Imi\Config;
use Imi\Event\IEventListener;

class LoadRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\LoadRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $config = Config::get('@app.imi.runtime', []);
        if (!($config['cli'] ?? true))
        {
            return;
        }
        $data = $e->data['cli'] ?? [];
        CliManager::setMap($data['cli'] ?? []);
    }
}
