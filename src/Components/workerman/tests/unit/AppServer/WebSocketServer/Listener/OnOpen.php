<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\WebSocketServer\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectionContext;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * @Listener("IMI.WORKERMAN.SERVER.WEBSOCKET.CONNECT")
 */
class OnOpen implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        ConnectionContext::set('requestUri', (string) ConnectionContext::get('uri'));
    }
}
