<?php

namespace Imi\Server\UdpServer\Middleware;

use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

interface IMiddleware
{
    public function process(IPacketData $data, IPacketHandler $handler);
}
