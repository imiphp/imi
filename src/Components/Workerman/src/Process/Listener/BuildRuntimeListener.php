<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Listener;

use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Workerman\Process\ProcessManager;

class BuildRuntimeListener implements IEventListener
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
        if (!Config::get('@app.imi.runtime.workerman.process', true))
        {
            return;
        }
        $eventData = $e->getData();
        $data = [];
        $data['process'] = ProcessManager::getMap();
        $eventData['data']['workermanProcess'] = $data;
    }
}
