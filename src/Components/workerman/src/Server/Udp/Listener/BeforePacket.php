<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Udp\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Server\Udp\Event\WorkermanUdpMessageEvent;

/**
 * Packet事件前置处理.
 */
#[Listener(eventName: WorkermanEvents::SERVER_UDP_MESSAGE, priority: \Imi\Util\ImiPriority::IMI_MAX)]
class BeforePacket implements IEventListener
{
    /**
     * @param WorkermanUdpMessageEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        // 中间件
        $dispatcher = RequestContext::getServerBean('UdpDispatcher');
        $dispatcher->dispatch($e->packetData);
    }
}
