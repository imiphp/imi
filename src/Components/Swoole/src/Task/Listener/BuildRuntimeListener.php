<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Task\TaskManager;

class BuildRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        if (!Config::get('@app.imi.runtime.swoole.task', true))
        {
            return;
        }
        $eventData = $e->getData();
        $data = [];
        $data['task'] = TaskManager::getMap();
        $eventData['data']['task'] = $data;
    }
}
