<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\TCPServer\MainServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Swoole\Server\TcpServer\Error\ITcpRouteNotFoundHandler;
use Imi\Swoole\Server\TcpServer\IReceiveHandler;
use Imi\Swoole\Server\TcpServer\Message\IReceiveData;

/**
 * @Bean("RouteNotFound")
 */
class RouteNotFound implements ITcpRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @param \Imi\Swoole\Server\TcpServer\Message\IReceiveData $data
     * @param \Imi\Swoole\Server\TcpServer\IReceiveHandler      $handler
     *
     * @return void
     */
    public function handle(IReceiveData $data, IReceiveHandler $handler)
    {
        return 'gg';
    }
}
