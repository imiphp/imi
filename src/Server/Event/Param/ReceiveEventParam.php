<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class ReceiveEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 客户端连接的标识符.
     *
     * @var int
     */
    public $fd;

    /**
     * Reactor线程ID.
     *
     * @var int
     */
    public $reactorID;

    /**
     * 接收到的数据.
     *
     * @var string
     */
    public $data;
}
