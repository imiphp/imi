<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Error;

use Imi\Swoole\Server\WebSocket\IMessageHandler;
use Imi\Swoole\Server\WebSocket\Message\IFrame;

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
