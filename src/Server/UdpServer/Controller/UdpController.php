<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Controller;

use Imi\Server\Contract\IServer;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * UDP 控制器.
 */
abstract class UdpController
{
    /**
     * 服务器.
     */
    public IServer $server;

    /**
     * 包数据.
     */
    public IPacketData $data;
}
