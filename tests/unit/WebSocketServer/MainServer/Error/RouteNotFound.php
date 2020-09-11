<?php

namespace Imi\Test\WebSocketServer\MainServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Server\WebSocket\Error\IWSRouteNotFoundHandler;
use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * @Bean("RouteNotFound")
 */
class RouteNotFound implements IWSRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @param IFrame          $frame
     * @param IMessageHandler $handler
     *
     * @return mixed
     */
    public function handle(IFrame $frame, IMessageHandler $handler)
    {
        return 'gg';
    }
}
