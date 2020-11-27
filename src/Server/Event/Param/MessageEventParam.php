<?php

namespace Imi\Server\Event\Param;

use Imi\Server\Base;
use Imi\Event\EventParam;
use Swoole\WebSocket\Frame;

class MessageEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public Base $server;

    /**
     * swoole 数据帧对象
     *
     * @var \Swoole\WebSocket\Frame
     */
    public Frame $frame;
}
