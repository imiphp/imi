<?php

namespace Imi\Server\UdpServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Server\Event\Listener\IPacketEventListener;
use Imi\Server\Event\Param\PacketEventParam;
use Imi\Server\UdpServer\Message\PacketData;
use Imi\Worker;

/**
 * Packet事件前置处理.
 *
 * @ClassEventListener(className="Imi\Server\UdpServer\Server",eventName="packet",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforePacket implements IPacketEventListener
{
    /**
     * 事件处理方法.
     *
     * @param PacketEventParam $e
     *
     * @return void
     */
    public function handle(PacketEventParam $e)
    {
        if (!Worker::isWorkerStartAppComplete())
        {
            return;
        }
        $clientInfo = $e->clientInfo;
        // 上下文创建
        RequestContext::muiltiSet([
            'clientInfo'    => $clientInfo,
            'server'        => $e->getTarget(),
        ]);

        // 中间件
        $dispatcher = RequestContext::getServerBean('UdpDispatcher');
        $dispatcher->dispatch(new PacketData($e->data, $clientInfo));
    }
}
