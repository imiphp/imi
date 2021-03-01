<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Workerman\Process\ProcessManager;

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
        $config = Config::get('@app.imi.runtime.workerman', []);
        if (!($config['process'] ?? true))
        {
            return;
        }
        $data = $e->getData()['data']['workermanProcess'] ?? [];
        ProcessManager::setMap($data['process']);
    }
}
