<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\IPacketEventListener;
use Imi\Swoole\Server\Event\Param\PacketEventParam;
use Imi\Swoole\Server\UdpServer\Message\PacketData;
use Imi\Swoole\SwooleWorker;

/**
 * Packet事件前置处理.
 */
#[ClassEventListener(className: \Imi\Swoole\Server\UdpServer\Server::class, eventName: 'packet', priority: \Imi\Util\ImiPriority::IMI_MAX)]
class BeforePacket implements IPacketEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(PacketEventParam $e): void
    {
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            return;
        }

        $clientInfo = $e->clientInfo;
        $requestContext = RequestContext::getContext();
        $requestContext['clientInfo'] = $clientInfo;
        $requestContext['server'] = $server = $e->getTarget();
        $packetData = $requestContext['packetData'] = new PacketData($clientInfo['address'], $clientInfo['port'], $e->data, $clientInfo);

        // 中间件
        $dispatcher = $server->getBean('UdpDispatcher');
        $dispatcher->dispatch($packetData);
    }
}
