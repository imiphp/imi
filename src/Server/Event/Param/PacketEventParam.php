<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class PacketEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 数据.
     *
     * @var string
     */
    public $data;

    /**
     * 客户端信息.
     *
     * @var array
     */
    public $clientInfo;
}
