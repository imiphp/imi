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
 * @Listener(eventName="IMI.WORKERMAN.SERVER.WEBSOCKET.MESSAGE", priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeMessage implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        ['frame' => $frame] = $e->getData();

        // 中间件
        $requestContext = RequestContext::getContext();
        /** @var \Imi\Server\WebSocket\Dispatcher $dispatcher */
        $dispatcher = $requestContext['server']->getBean('WebSocketDispatcher');
        $requestContext['frame'] = $frame;
        $dispatcher->dispatch($frame);
    }
}
