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
     * {@inheritDoc}
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

        // 中间件
        $requestContext = RequestContext::getContext();
        /** @var \Imi\Server\WebSocket\Dispatcher $dispatcher */
        $dispatcher = $requestContext['server']->getBean('WebSocketDispatcher');
        $imiFrame = new Frame($frame);
        $requestContext['frame'] = $imiFrame;
        $dispatcher->dispatch($imiFrame);
    }
}
