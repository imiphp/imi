<?php

namespace Imi\Server\WebSocket\Middleware;

use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Message\IFrame;

interface IMiddleware
{
    public function process(IFrame $frame, IMessageHandler $handler);
}
