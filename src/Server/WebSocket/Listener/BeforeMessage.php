<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\App;
use Imi\Worker;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Util\Coroutine;
use Imi\Server\WebSocket\Message\Frame;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\MessageEventParam;
use Imi\Server\Event\Listener\IMessageEventListener;

/**
 * Message事件前置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="message",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeMessage implements IMessageEventListener
{
    /**
     * 事件处理方法
     * @param MessageEventParam $e
     * @return void
     */
    public function handle(MessageEventParam $e)
    {
        $frame = $e->frame;
        if(!Worker::isWorkerStartAppComplete())
        {
            $e->server->getSwooleServer()->close($frame->fd);
            $e->stopPropagation();
            return;
        }
        // 上下文创建
        RequestContext::muiltiSet([
            'fd'        =>  $frame->fd,
            'server'    =>  $e->getTarget(),
        ]);

        // 中间件
        $dispatcher = RequestContext::getServerBean('WebSocketDispatcher');
        $dispatcher->dispatch(new Frame($frame));

    }
}