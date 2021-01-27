<?php

namespace Imi\Controller;

/**
 * UDP 控制器.
 */
abstract class UdpController
{
    /**
     * 服务器.
     *
     * @var \Imi\Server\UdpServer\Server
     */
    public $server;

    /**
     * 包数据.
     *
     * @var \Imi\Server\UdpServer\Message\IPacketData
     */
    public $data;
}
