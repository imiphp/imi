<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Message;

use Imi\Server\WebSocket\Message\IFrame;

interface ISwooleWebSocketFrame extends IFrame
{
    /**
     * 获取 \Swoole\Websocket\Frame 对象
     */
    public function getSwooleWebSocketFrame(): \Swoole\Websocket\Frame;
}
