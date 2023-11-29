<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Listener;

use Imi\Config;
use Imi\Event\IEventListener;
use Imi\Workerman\Process\ProcessManager;

class BuildRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\BuildRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.workerman.process', true))
        {
            return;
        }
        $data = [];
        $data['process'] = ProcessManager::getMap();
        $e->data['workermanProcess'] = $data;
    }
}
