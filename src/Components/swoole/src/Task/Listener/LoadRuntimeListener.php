<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Task\TaskManager;

class LoadRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $config = Config::get('@app.imi.runtime.swoole', []);
        if (!($config['task'] ?? true))
        {
            return;
        }
        $data = $e->getData()['data']['task'] ?? [];
        TaskManager::setMap($data['task'] ?? []);
    }
}
