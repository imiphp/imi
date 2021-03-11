<?php

namespace Imi\Server\WebSocket\Middleware;

use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Message\IFrame;

interface IMiddleware
{
    /**
     * @param \Imi\Server\WebSocket\Message\IFrame  $frame
     * @param \Imi\Server\WebSocket\IMessageHandler $handler
     *
     * @return mixed
     */
    public function process(IFrame $frame, IMessageHandler $handler);
}
