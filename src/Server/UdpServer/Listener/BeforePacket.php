<?php
namespace Imi\Server\UdpServer\Listener;

use Imi\Worker;
use Imi\ServerManage;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\PacketEventParam;
use Imi\Server\UdpServer\Message\PacketData;
use Imi\Server\Event\Listener\IPacketEventListener;

/**
 * Packet事件前置处理
 * @ClassEventListener(className="Imi\Server\UdpServer\Server",eventName="packet",priority=PHP_INT_MAX)
 */
class BeforePacket implements IPacketEventListener
{
    /**
     * 事件处理方法
     * @param PacketEventParam $e
     * @return void
     */
    public function handle(PacketEventParam $e)
    {
        if(!Worker::isWorkerStartAppComplete())
        {
            $GLOBALS['WORKER_START_END_RESUME_COIDS'][] = Coroutine::getuid();
            Coroutine::suspend();
        }
        // 上下文创建
        RequestContext::create();
        RequestContext::set('clientInfo', $e->clientInfo);
        RequestContext::set('server', $e->getTarget());

        // 中间件
        $dispatcher = RequestContext::getServerBean('UdpDispatcher');
        $dispatcher->dispatch(new PacketData($e->data, $e->clientInfo));
    }
}