<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Controller;

use Imi\Swoole\Server\UdpServer\Message\IPacketData;
use Imi\Swoole\Server\UdpServer\Server;

/**
 * UDP 控制器.
 */
abstract class UdpController
{
    /**
     * 请求
     *
     * @var \Imi\Swoole\Server\UdpServer\Server
     */
    public Server $server;

    /**
     * 桢.
     *
     * @var \Imi\Swoole\Server\UdpServer\Message\IPacketData
     */
    public IPacketData $data;
}
