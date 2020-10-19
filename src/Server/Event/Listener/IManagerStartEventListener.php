<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\ManagerStartEventParam;

/**
 * 监听服务器ManagerStart事件接口.
 */
interface IManagerStartEventListener
{
    /**
     * 事件处理方法.
     *
     * @param StartEventParam $e
     *
     * @return void
     */
    public function handle(ManagerStartEventParam $e);
}
