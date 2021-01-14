<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\UDPServer\MainServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Swoole\Server\UdpServer\IPacketHandler;
use Imi\Swoole\Server\UdpServer\Message\IPacketData;
use Imi\Swoole\Server\UdpServer\Middleware\IMiddleware;

/**
 * @Bean
 */
class Test implements IMiddleware
{
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        RequestContext::set('middlewareData', 'imi');

        return $handler->handle($data, $handler);
    }
}
