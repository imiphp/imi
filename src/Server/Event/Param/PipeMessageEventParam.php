<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Server\Base;

class PipeMessageEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public Base $server;

    /**
     * Worker进程ID.
     *
     * @var int
     */
    public int $workerId;

    /**
     * 消息内容，可以是任意PHP类型.
     *
     * @var mixed
     */
    public $message;
}
