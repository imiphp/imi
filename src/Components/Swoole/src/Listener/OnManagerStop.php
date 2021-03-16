<?php

declare(strict_types=1);

namespace Imi\Swoole\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\IManagerStopEventListener;
use Imi\Swoole\Server\Event\Param\ManagerStopEventParam;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.MANAGER.STOP",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class OnManagerStop implements IManagerStopEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(ManagerStopEventParam $e): void
    {
        App::getBean('Logger')->save();
    }
}
