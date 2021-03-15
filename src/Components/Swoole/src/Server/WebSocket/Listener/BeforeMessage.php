<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\IMessageEventListener;
use Imi\Swoole\Server\Event\Param\MessageEventParam;
use Imi\Swoole\Server\WebSocket\Message\Frame;
use Imi\Swoole\SwooleWorker;

/**
 * Message事件前置处理.
 *
 * @ClassEventListener(className="Imi\Swoole\Server\WebSocket\Server",eventName="message",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeMessage implements IMessageEventListener
{
    /**
     * 事件处理方法.
     *
     * @param MessageEventParam $e
     *
     * @return void
     */
    public function handle(MessageEventParam $e): void
    {
        $frame = $e->frame;
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $e->server->getSwooleServer()->close($frame->fd);
            $e->stopPropagation();

            return;
        }
        // 上下文创建
        RequestContext::muiltiSet([
            'fd'        => $frame->fd,
            'server'    => $e->getTarget(),
        ]);

        // 中间件
        $dispatcher = RequestContext::getServerBean('WebSocketDispatcher');
        $dispatcher->dispatch(new Frame($frame));
    }
}
