<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Swoole\WebSocket\Frame;

class MessageEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public ISwooleServer $server;

    /**
     * swoole 数据帧对象
     */
    public Frame $frame;
}
