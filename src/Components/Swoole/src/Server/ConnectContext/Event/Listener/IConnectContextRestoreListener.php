<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectContext\Event\Listener;

use Imi\Swoole\Server\ConnectContext\Event\Param\ConnectContextRestoreParam;

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
