<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Process\ProcessPoolManager;

class BuildRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        if (!Config::get('@app.imi.runtime.swoole.process', true))
        {
            return;
        }
        $eventData = $e->getData();
        $data = [];
        $data['process'] = ProcessManager::getMap();
        $data['processPool'] = ProcessPoolManager::getMap();
        $eventData['data']['process'] = $data;
    }
}
