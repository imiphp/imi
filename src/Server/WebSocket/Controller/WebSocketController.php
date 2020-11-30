<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Controller;

use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\Server;

/**
 * WebSocket 控制器.
 */
abstract class WebSocketController
{
    /**
     * 请求
     *
     * @var \Imi\Server\WebSocket\Server
     */
    public Server $server;

    /**
     * 桢.
     *
     * @var \Imi\Server\WebSocket\Message\IFrame
     */
    public IFrame $frame;
}
