<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectionContext;
use Imi\Event\IEventListener;
use Imi\Workerman\Event\WorkermanEvents;

#[Listener(eventName: WorkermanEvents::SERVER_WEBSOCKET_CONNECT)]
class WorkermanOnOpen implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        ConnectionContext::set('requestUri', (string) ConnectionContext::get('uri'));
    }
}
