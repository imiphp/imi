<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Controller;

use Imi\Server\UdpServer\Server;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * UDP 控制器.
 */
abstract class UdpController
{
    /**
     * 请求
     *
     * @var \Imi\Server\UdpServer\Server
     */
    public Server $server;

    /**
     * 桢.
     *
     * @var \Imi\Server\UdpServer\Message\IPacketData
     */
    public IPacketData $data;
}
