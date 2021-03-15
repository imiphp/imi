<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\IConnectEventListener;
use Imi\Swoole\Server\Event\Param\ConnectEventParam;
use Imi\Swoole\SwooleWorker;

/**
 * Connect事件前置处理.
 *
 * @ClassEventListener(className="Imi\Swoole\Server\TcpServer\Server",eventName="connect",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeConnect implements IConnectEventListener
{
    /**
     * 事件处理方法.
     *
     * @param ConnectEventParam $e
     *
     * @return void
     */
    public function handle(ConnectEventParam $e): void
    {
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $e->server->getSwooleServer()->close($e->fd);
            $e->stopPropagation();

            return;
        }
        // 上下文创建
        RequestContext::muiltiSet([
            'server'    => $e->server,
            'fd'        => $e->fd,
        ]);

        // 连接上下文创建
        ConnectContext::create();
    }
}
