<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\ManagerStopEventParam;

/**
 * 监听服务器ManagerStop事件接口.
 */
interface IManagerStopEventListener
{
    /**
     * 事件处理方法.
     *
     * @param StopEventParam $e
     *
     * @return void
     */
    public function handle(ManagerStopEventParam $e);
}
