<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\MainServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Swoole\Server\WebSocket\Error\IWSRouteNotFoundHandler;
use Imi\Swoole\Server\WebSocket\IMessageHandler;
use Imi\Swoole\Server\WebSocket\Message\IFrame;

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
