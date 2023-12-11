<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Listener;

use Imi\Config;
use Imi\Event\IEventListener;
use Imi\Workerman\Process\ProcessManager;

class LoadRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\LoadRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $config = Config::get('@app.imi.runtime.workerman', []);
        if (!($config['process'] ?? true))
        {
            return;
        }
        $data = $e->data['workermanProcess'] ?? [];
        ProcessManager::setMap($data['process'] ?? []);
    }
}
