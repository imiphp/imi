<?php
namespace Imi\Server\UdpServer\Listener;

use Imi\ServerManage;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\PacketEventParam;
use Imi\Server\Event\Listener\IPacketEventListener;

/**
 * Packet事件后置处理
 * @ClassEventListener(className="Imi\Server\UdpServer\Server",eventName="packet",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class AfterPacket implements IPacketEventListener
{
    /**
     * 事件处理方法
     * @param PacketEventParam $e
     * @return void
     */
    public function handle(PacketEventParam $e)
    {
    }
}