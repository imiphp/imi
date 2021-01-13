<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Middleware;

use Imi\Swoole\Server\UdpServer\IPacketHandler;
use Imi\Swoole\Server\UdpServer\Message\IPacketData;

interface IMiddleware
{
    public function process(IPacketData $data, IPacketHandler $handler);
}
