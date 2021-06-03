<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\PacketEventParam;

/**
 * 监听服务器 Packet 事件接口.
 */
interface IPacketEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(PacketEventParam $e): void;
}
