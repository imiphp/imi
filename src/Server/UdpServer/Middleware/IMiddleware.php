<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Middleware;

use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

interface IMiddleware
{
    public function process(IPacketData $data, IPacketHandler $handler);
}
