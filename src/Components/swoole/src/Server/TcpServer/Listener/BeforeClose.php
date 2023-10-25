<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\ICloseEventListener;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\SwooleWorker;

/**
 * Close事件前置处理.
 */
#[ClassEventListener(className: \Imi\Swoole\Server\TcpServer\Server::class, eventName: 'close', priority: 19940312)]
class BeforeClose implements ICloseEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(CloseEventParam $e): void
    {
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $e->stopPropagation();

            return;
        }
        RequestContext::muiltiSet([
            'clientId'        => $e->clientId,
            'server'          => $e->getTarget(),
        ]);
    }
}
