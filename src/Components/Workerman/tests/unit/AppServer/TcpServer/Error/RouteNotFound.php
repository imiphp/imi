<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\TcpServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Server\TcpServer\Error\ITcpRouteNotFoundHandler;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * @Bean("TcpRouteNotFound")
 */
class RouteNotFound implements ITcpRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @return mixed
     */
    public function handle(IReceiveData $data, IReceiveHandler $handler)
    {
        return 'gg';
    }
}
