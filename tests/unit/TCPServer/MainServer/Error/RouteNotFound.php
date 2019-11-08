<?php
namespace Imi\Test\TCPServer\MainServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;
use Imi\Server\TcpServer\Error\ITcpRouteNotFoundHandler;

/**
 * @Bean("RouteNotFound")
 */
class RouteNotFound implements ITcpRouteNotFoundHandler
{
    /**
     * 处理方法
     *
     * @param \Imi\Server\TcpServer\Message\IReceiveData $data
     * @param \Imi\Server\TcpServer\IReceiveHandler $handler
     * @return void
     */
    public function handle(IReceiveData $data, IReceiveHandler $handler)
    {
        return 'gg';
    }

}
