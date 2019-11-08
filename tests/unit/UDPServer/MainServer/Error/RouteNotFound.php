<?php
namespace Imi\Test\UDPServer\MainServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;
use Imi\Server\UdpServer\Error\IUdpRouteNotFoundHandler;

/**
 * @Bean("RouteNotFound")
 */
class RouteNotFound implements IUdpRouteNotFoundHandler
{
    /**
     * 处理方法
     *
     * @param \Imi\Server\UdpServer\Message\IPacketData $data
     * @param \Imi\Server\UdpServer\IPacketHandler $handler
     * @return void
     */
    public function handle(IPacketData $data, IPacketHandler $handler)
    {
        return 'gg';
    }

}
