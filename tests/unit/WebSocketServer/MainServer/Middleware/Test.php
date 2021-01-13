<?php

declare(strict_types=1);

namespace Imi\Test\WebSocketServer\MainServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Swoole\Server\WebSocket\IMessageHandler;
use Imi\Swoole\Server\WebSocket\Message\IFrame;
use Imi\Swoole\Server\WebSocket\Middleware\IMiddleware;

/**
 * @Bean
 */
class Test implements IMiddleware
{
    public function process(IFrame $frame, IMessageHandler $handler)
    {
        RequestContext::set('middlewareData', 'imi');

        return $handler->handle($frame);
    }
}
