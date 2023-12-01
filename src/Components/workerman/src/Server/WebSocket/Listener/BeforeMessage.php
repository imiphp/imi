<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Server\WebSocket\Event\WorkermanWebSocketMessageEvent;

/**
 * Message事件前置处理.
 */
#[Listener(eventName: WorkermanEvents::SERVER_WEBSOCKET_MESSAGE, priority: \Imi\Util\ImiPriority::IMI_MAX)]
class BeforeMessage implements IEventListener
{
    /**
     * @param WorkermanWebSocketMessageEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        // 中间件
        $requestContext = RequestContext::getContext();
        /** @var \Imi\Server\WebSocket\Dispatcher $dispatcher */
        $dispatcher = $requestContext['server']->getBean('WebSocketDispatcher');
        $requestContext['frame'] = $e->frame;
        $dispatcher->dispatch($e->frame);
    }
}
