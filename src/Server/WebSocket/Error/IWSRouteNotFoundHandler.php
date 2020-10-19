<?php

namespace Imi\Server\WebSocket\Error;

use Imi\Server\WebSocket\IMessageHandler;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * 处理未找到 WebSocket 路由情况的接口.
 */
interface IWSRouteNotFoundHandler
{
    /**
     * 处理方法.
     *
     * @param IFrame          $frame
     * @param IMessageHandler $handler
     *
     * @return mixed
     */
    public function handle(IFrame $frame, IMessageHandler $handler);
}
