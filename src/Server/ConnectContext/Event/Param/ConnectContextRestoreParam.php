<?php

namespace Imi\Server\ConnectContext\Event\Param;

use Imi\Event\EventParam;

/**
 * 连接上下文数据恢复事件参数.
 */
class ConnectContextRestoreParam extends EventParam
{
    /**
     * 数据原始连接号.
     *
     * @var int
     */
    public $fromFd;

    /**
     * 数据目标连接号（当前连接号）.
     *
     * @var int
     */
    public $toFd;
}
