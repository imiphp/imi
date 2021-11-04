<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Udp\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * Packet事件前置处理.
 *
 * @Listener(eventName="IMI.WORKERMAN.SERVER.UDP.MESSAGE", priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforePacket implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        /** @var IPacketData $packetData */
        ['packetData' => $packetData] = $e->getData();

        // 中间件
        $dispatcher = RequestContext::getServerBean('UdpDispatcher');
        $dispatcher->dispatch($packetData);
    }
}
