<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Middleware;

use Imi\Swoole\Server\WebSocket\IMessageHandler;
use Imi\Swoole\Server\WebSocket\Message\IFrame;

interface IMiddleware
{
    public function process(IFrame $frame, IMessageHandler $handler);
}
