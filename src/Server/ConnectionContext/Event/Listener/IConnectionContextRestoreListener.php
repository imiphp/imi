<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext\Event\Listener;

use Imi\Server\ConnectionContext\Event\Param\ConnectionContextRestoreParam;

/**
 * 连接上下文数据恢复事件监听.
 */
interface IConnectionContextRestoreListener
{
    /**
     * 事件处理方法.
     */
    public function handle(ConnectionContextRestoreParam $e): void;
}
