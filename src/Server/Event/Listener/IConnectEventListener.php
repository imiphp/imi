<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\ConnectEventParam;

/**
 * 监听服务器connect事件接口.
 */
interface IConnectEventListener
{
    /**
     * 事件处理方法.
     *
     * @param ConnectEventParam $e
     *
     * @return void
     */
    public function handle(ConnectEventParam $e);
}
