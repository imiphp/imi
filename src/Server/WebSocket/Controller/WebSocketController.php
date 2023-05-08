<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Controller;

use Imi\RequestContext;
use Imi\Server\WebSocket\Contract\IWebSocketServer;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * WebSocket 控制器.
 */
abstract class WebSocketController
{
    /**
     * 服务器对象
     */
    public ?IWebSocketServer $server = null;

    /**
     * 桢.
     */
    public IFrame $frame;

    public function __construct()
    {
        // @phpstan-ignore-next-line
        $server = $this->server = RequestContext::getServer();
        // @phpstan-ignore-next-line
        $this->frame = $server->getBean('WebSocketFrameProxy');
    }
}
