<?php

declare(strict_types=1);

namespace Imi\Aop\Listener;

use Imi\Aop\AopAnnotationLoader;
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
        $data = $e->getData()['data']['aop'] ?? [];
        AopAnnotationLoader::setMap($data['map'] ?? []);
    }
}
