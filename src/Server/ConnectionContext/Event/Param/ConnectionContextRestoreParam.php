<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Server\ConnectionContext\Event\ConnectionContextEvents;

/**
 * 连接上下文数据恢复事件参数.
 */
class ConnectionContextRestoreParam extends CommonEvent
{
    public function __construct(
        /**
         * 数据原始连接号.
         */
        public readonly int $fromClientId = 0,
        /**
         * 数据目标连接号（当前连接号）.
         */
        public readonly int $toClientId = 0,
        /**
         * 服务器名.
         */
        public readonly ?string $serverName = null)
    {
        parent::__construct(ConnectionContextEvents::RESTORE);
    }
}
