<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext\Event\Param;

use Imi\Event\EventParam;

/**
 * 连接上下文数据恢复事件参数.
 */
class ConnectionContextRestoreParam extends EventParam
{
    /**
     * 数据原始连接号.
     */
    public int $fromClientId = 0;

    /**
     * 数据目标连接号（当前连接号）.
     */
    public int $toClientId = 0;
}
