<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Controller;

use Imi\Server\Annotation\ServerInject;
use Imi\Server\Contract\IServer;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * WebSocket 控制器.
 */
abstract class WebSocketController
{
    /**
     * 服务器对象
     */
    public IServer $server;

    /**
     * 桢.
     *
     * @ServerInject("WebSocketFrameProxy")
     */
    public IFrame $frame;

    public function __construct(IServer $server)
    {
        $this->server = $server;
    }
}
