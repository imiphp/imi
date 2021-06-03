<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\Middleware\IMiddleware;

/**
 * @Bean
 */
class Test implements IMiddleware
{
    /**
     * @return mixed
     */
    public function process(IFrame $frame, IMessageHandler $handler)
    {
        RequestContext::set('middlewareData', 'imi');

        return $handler->handle($frame);
    }
}
