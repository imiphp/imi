<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Middleware;

use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

interface IMiddleware
{
    /**
     * @param \Imi\Server\UdpServer\Message\IPacketData $data
     * @param \Imi\Server\UdpServer\IPacketHandler      $handler
     *
     * @return mixed
     */
    public function process(IPacketData $data, IPacketHandler $handler);
}
