<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Controller;

use Imi\Server\WebSocket\Contract\IWebSocketServer;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * WebSocket 控制器.
 */
abstract class WebSocketController
{
    /**
     * 服务器.
     *
     * @var \Imi\Server\WebSocket\Contract\IWebSocketServer
     */
    public IWebSocketServer $server;

    /**
     * 桢.
     *
     * @var \Imi\Server\WebSocket\Message\IFrame
     */
    public IFrame $frame;
}
