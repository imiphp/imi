<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Process\ProcessManager;
use Imi\Swoole\Process\ProcessPoolManager;

class LoadRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        $config = Config::get('@app.imi.runtime.swoole', []);
        if (!($config['process'] ?? true))
        {
            return;
        }
        $data = $e->getData()['data']['process'] ?? [];
        ProcessManager::setMap($data['process']);
        ProcessPoolManager::setMap($data['processPool']);
    }
}
