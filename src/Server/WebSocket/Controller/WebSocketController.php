<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Controller;

use Imi\Server\Annotation\ServerInject;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * WebSocket 控制器.
 */
abstract class WebSocketController
{
    /**
     * 桢.
     *
     * @ServerInject("WebSocketFrameProxy")
     */
    public IFrame $frame;
}
