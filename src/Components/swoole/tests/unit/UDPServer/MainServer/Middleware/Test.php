<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\UDPServer\MainServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;
use Imi\Server\UdpServer\Middleware\IMiddleware;

/**
 * @Bean
 */
class Test implements IMiddleware
{
    /**
     * {@inheritDoc}
     */
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        RequestContext::set('middlewareData', 'imi');

        return $handler->handle($data);
    }
}
