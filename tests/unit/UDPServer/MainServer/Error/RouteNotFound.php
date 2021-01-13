<?php

declare(strict_types=1);

namespace Imi\Test\UDPServer\MainServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Swoole\Server\UdpServer\Error\IUdpRouteNotFoundHandler;
use Imi\Swoole\Server\UdpServer\IPacketHandler;
use Imi\Swoole\Server\UdpServer\Message\IPacketData;

/**
 * @Bean("RouteNotFound")
 */
class RouteNotFound implements IUdpRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @param \Imi\Swoole\Server\UdpServer\Message\IPacketData $data
     * @param \Imi\Swoole\Server\UdpServer\IPacketHandler      $handler
     *
     * @return void
     */
    public function handle(IPacketData $data, IPacketHandler $handler)
    {
        return 'gg';
    }
}
