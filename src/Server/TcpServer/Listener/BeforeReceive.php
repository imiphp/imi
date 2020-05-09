<?php
namespace Imi\Server\TcpServer\Listener;

use Imi\App;
use Imi\Worker;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\ReceiveEventParam;
use Imi\Server\TcpServer\Message\ReceiveData;
use Imi\Server\Event\Listener\IReceiveEventListener;

/**
 * Receive事件前置处理
 * @ClassEventListener(className="Imi\Server\TcpServer\Server",eventName="receive",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeReceive implements IReceiveEventListener
{
    /**
     * 事件处理方法
     * @param ReceiveEventParam $e
     * @return void
     */
    public function handle(ReceiveEventParam $e)
    {
        $fd = $e->fd;
        if(!Worker::isWorkerStartAppComplete())
        {
            $e->server->getSwooleServer()->close($fd);
            $e->stopPropagation();
            return;
        }
        // 上下文创建
        RequestContext::muiltiSet([
            'server'    =>  $e->getTarget(),
            'fd'        =>  $fd,
        ]);

        // 中间件
        $dispatcher = RequestContext::getServerBean('TcpDispatcher');
        $dispatcher->dispatch(new ReceiveData($fd, $e->reactorID, $e->data));

    }
}