<?php

declare(strict_types=1);

namespace Imi\Aop\Listener;

use Imi\Aop\AopManager;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

class LoadRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        if (!Config::get('@app.imi.runtime.aop', true))
        {
            return;
        }
        $eventData = $e->getData();
        $data = $eventData['data']['aop'] ?? [];
        if ($cache = ($data['cache'] ?? null))
        {
            AopManager::setArrayCache($cache);
        }
        if ($cache = ($data['dynamicRulesCache'] ?? null))
        {
            AopManager::setDynamicRulesCache($cache);
        }
    }
}
