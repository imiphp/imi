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
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        if (!Config::get('@app.imi.runtime.aop', true))
        {
            return;
        }
        $eventData = $e->getData();
        $data = $eventData['data']['aop'] ?? [];
        if ((bool) ($cache = ($data['cache'] ?? null)) | (bool) ($dynamicRulesCache = ($data['dynamicRulesCache'] ?? null)))
        {
            AopManager::clear();
            if ($cache)
            {
                AopManager::setArrayCache($cache);
            }
            if ($dynamicRulesCache)
            {
                AopManager::setDynamicRulesCache($dynamicRulesCache);
            }
        }
    }
}
