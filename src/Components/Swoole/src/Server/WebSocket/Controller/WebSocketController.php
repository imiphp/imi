<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Controller;

use Imi\Swoole\Server\WebSocket\Message\IFrame;
use Imi\Swoole\Server\WebSocket\Server;

/**
 * WebSocket 控制器.
 */
abstract class WebSocketController
{
    /**
     * 请求
     *
     * @var \Imi\Swoole\Server\WebSocket\Server
     */
    public Server $server;

    /**
     * 桢.
     *
     * @var \Imi\Swoole\Server\WebSocket\Message\IFrame
     */
    public IFrame $frame;
}
