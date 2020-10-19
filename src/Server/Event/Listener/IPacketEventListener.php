<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\PacketEventParam;

/**
 * 监听服务器 Packet 事件接口.
 */
interface IPacketEventListener
{
    /**
     * 事件处理方法.
     *
     * @param PacketEventParam $e
     *
     * @return void
     */
    public function handle(PacketEventParam $e);
}
