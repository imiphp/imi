<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Controller;

use Imi\Server\Annotation\ServerInject;
use Imi\Server\Contract\IServer;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * UDP 控制器.
 */
abstract class UdpController
{
    /**
     * 服务器对象
     */
    public IServer $server;

    /**
     * 包数据.
     *
     * @ServerInject("UdpPacketDataProxy")
     */
    public IPacketData $data;

    public function __construct(IServer $server)
    {
        $this->server = $server;
    }
}
