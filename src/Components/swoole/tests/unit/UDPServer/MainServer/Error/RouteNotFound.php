<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\UDPServer\MainServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Server\UdpServer\Error\IUdpRouteNotFoundHandler;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * @Bean("RouteNotFound")
 */
class RouteNotFound implements IUdpRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @return mixed
     */
    public function handle(IPacketData $data, IPacketHandler $handler)
    {
        return 'gg';
    }
}
