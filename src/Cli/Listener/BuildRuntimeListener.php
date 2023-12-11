<?php

declare(strict_types=1);

namespace Imi\Cli\Listener;

use Imi\Cli\CliManager;
use Imi\Config;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\BuildRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.cli', true))
        {
            return;
        }
        $data = [];
        $data['cli'] = CliManager::getMap();
        $e->data['cli'] = $data;
    }
}
