<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Listener;

use Imi\Config;
use Imi\Event\IEventListener;
use Imi\Swoole\Task\TaskManager;

class BuildRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\BuildRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.swoole.task', true))
        {
            return;
        }
        $data = [];
        $data['task'] = TaskManager::getMap();
        $e->data['task'] = $data;
    }
}
