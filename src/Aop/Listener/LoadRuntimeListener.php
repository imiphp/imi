<?php

declare(strict_types=1);

namespace Imi\Aop\Listener;

use Imi\Aop\AopManager;
use Imi\Config;
use Imi\Event\IEventListener;

class LoadRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\LoadRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.aop', true))
        {
            return;
        }
        $data = $e->data['aop'] ?? [];
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
