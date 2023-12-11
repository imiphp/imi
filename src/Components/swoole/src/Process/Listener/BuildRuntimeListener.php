<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Listener;

use Imi\Config;
use Imi\Event\IEventListener;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Process\ProcessPoolManager;

class BuildRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\BuildRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.swoole.process', true))
        {
            return;
        }
        $data = [];
        $data['process'] = ProcessManager::getMap();
        $data['processPool'] = ProcessPoolManager::getMap();
        $e->data['process'] = $data;
    }
}
