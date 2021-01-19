<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\IReceiveEventListener;
use Imi\Swoole\Server\Event\Param\ReceiveEventParam;
use Imi\Swoole\Server\TcpServer\Message\ReceiveData;
use Imi\Swoole\SwooleWorker;

/**
 * Receive事件前置处理.
 *
 * @ClassEventListener(className="Imi\Swoole\Server\TcpServer\Server",eventName="receive",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeReceive implements IReceiveEventListener
{
    /**
     * 事件处理方法.
     *
     * @param ReceiveEventParam $e
     *
     * @return void
     */
    public function handle(ReceiveEventParam $e)
    {
        $fd = $e->fd;
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $e->server->getSwooleServer()->close($fd);
            $e->stopPropagation();

            return;
        }
        // 上下文创建
        RequestContext::muiltiSet([
            'server'    => $e->getTarget(),
            'fd'        => $fd,
        ]);

        // 中间件
        $dispatcher = RequestContext::getServerBean('TcpDispatcher');
        $dispatcher->dispatch(new ReceiveData($fd, $e->reactorId, $e->data));
    }
}
