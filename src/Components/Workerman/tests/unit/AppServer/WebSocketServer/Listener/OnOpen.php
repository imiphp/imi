<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\WebSocketServer\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectContext;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * @Listener("IMI.WORKERMAN.SERVER.WEBSOCKET.CONNECT")
 */
class OnOpen implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        ConnectContext::set('requestUri', (string) ConnectContext::get('uri'));
    }
}
