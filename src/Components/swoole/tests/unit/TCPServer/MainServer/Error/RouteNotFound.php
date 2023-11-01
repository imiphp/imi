<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\TCPServer\MainServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Server\TcpServer\Error\ITcpRouteNotFoundHandler;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;

#[Bean(name: 'RouteNotFound')]
class RouteNotFound implements ITcpRouteNotFoundHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(IReceiveData $data, IReceiveHandler $handler)
    {
        return 'gg';
    }
}
