<?php

declare(strict_types=1);

namespace Imi\Event\Listener;

use Imi\Config;
use Imi\Event\ClassEventManager;
use Imi\Event\EventManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

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
        $config = Config::get('@app.imi.runtime', []);
        if (!($config['event'] ?? true))
        {
            return;
        }
        $data = $e->getData()['data']['event'] ?? [];
        EventManager::setMap($data['event']);
        ClassEventManager::setMap($data['classEvent']);
    }
}
