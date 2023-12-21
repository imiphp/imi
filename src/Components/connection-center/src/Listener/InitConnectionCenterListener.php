<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectionCenter\Facade\ConnectionCenter;
use Imi\Event\Contract\IEvent;
use Imi\Event\IEventListener;
use Imi\Process\Event\ProcessEvents;
use Imi\Server\Event\ServerEvents;

#[Listener(eventName: ProcessEvents::PROCESS_BEGIN)]
#[Listener(eventName: ServerEvents::WORKER_START)]
class InitConnectionCenterListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(IEvent $e): void
    {
        // 构造方法会自动初始化，从配置中加载
        ConnectionCenter::__getFacadeInstance();
    }
}
