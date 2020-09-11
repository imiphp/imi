<?php

namespace Imi\Server\ConnectContext\Event\Listener;

use Imi\Server\ConnectContext\Event\Param\ConnectContextRestoreParam;

/**
 * 连接上下文数据恢复事件监听.
 */
interface IConnectContextRestoreListener
{
    /**
     * 事件处理方法.
     *
     * @param ConnectContextRestoreParam $e
     *
     * @return void
     */
    public function handle(ConnectContextRestoreParam $e);
}
