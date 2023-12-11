<?php

declare(strict_types=1);

namespace Imi\Aop\Listener;

use Imi\Aop\AopManager;
use Imi\Config;
use Imi\Event\IEventListener;

class BuildRuntimeListener implements IEventListener
{
    /**
     * @param \Imi\Core\Runtime\Event\BuildRuntimeInfoEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!Config::get('@app.imi.runtime.aop', true))
        {
            return;
        }
        $e->data['aop'] = [
            'cache'             => AopManager::getArrayCache(),
            'dynamicRulesCache' => AopManager::getDynamicRulesCache(),
        ];
    }
}
