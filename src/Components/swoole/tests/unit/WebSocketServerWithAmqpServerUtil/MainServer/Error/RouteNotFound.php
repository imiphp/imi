<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithAmqpServerUtil\MainServer\Error;

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
     * {@inheritDoc}
     */
    public function handle(IFrame $frame, IMessageHandler $handler)
    {
        return 'gg';
    }
}
