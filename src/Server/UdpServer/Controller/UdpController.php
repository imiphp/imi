<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Controller;

use Imi\RequestContext;
use Imi\Server\UdpServer\Contract\IUdpServer;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * UDP 控制器.
 */
abstract class UdpController
{
    /**
     * 服务器对象
     */
    public ?IUdpServer $server = null;

    /**
     * 包数据.
     */
    public IPacketData $data;

    public function __construct()
    {
        // @phpstan-ignore-next-line
        $server = $this->server = RequestContext::getServer();
        // @phpstan-ignore-next-line
        $this->data = $server->getBean('UdpPacketDataProxy');
    }
}
