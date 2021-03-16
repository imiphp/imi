<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;

/**
 * Message事件前置处理.
 *
 * @Listener(eventName="IMI.WORKERMAN.SERVER.WEBSOCKET.MESSAGE",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeMessage implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        ['frame' => $frame] = $e->getData();

        // 中间件
        /** @var \Imi\Server\WebSocket\Dispatcher $dispatcher */
        $dispatcher = RequestContext::getServerBean('WebSocketDispatcher');
        $dispatcher->dispatch($frame);
    }
}
