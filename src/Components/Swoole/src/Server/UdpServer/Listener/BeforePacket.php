<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\IPacketEventListener;
use Imi\Swoole\Server\Event\Param\PacketEventParam;
use Imi\Swoole\Server\UdpServer\Message\PacketData;
use Imi\Swoole\Worker;

/**
 * Packet事件前置处理.
 *
 * @ClassEventListener(className="Imi\Swoole\Server\UdpServer\Server",eventName="packet",priority=Imi\Util\ImiPriority::IMI_MAX)
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
