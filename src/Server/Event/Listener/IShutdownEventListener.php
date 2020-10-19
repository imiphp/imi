<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\ShutdownEventParam;

/**
 * 监听服务器shutdown事件接口.
 */
interface IShutdownEventListener
{
    /**
     * 事件处理方法.
     *
     * @param ShutdownEventParam $e
     *
     * @return void
     */
    public function handle(ShutdownEventParam $e);
}
