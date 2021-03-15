<?php

declare(strict_types=1);

namespace Imi\Enum\Listener;

use Imi\Config;
use Imi\Enum\EnumManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e): void
    {
        if (!Config::get('@app.imi.runtime.enum', true))
        {
            return;
        }
        $eventData = $e->getData();
        $data = [];
        $data['enum'] = EnumManager::getMap();
        $eventData['data']['enum'] = $data;
    }
}
