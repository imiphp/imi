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
     * {@inheritDoc}
     */
    public function handle(ReceiveEventParam $e): void
    {
        $clientId = $e->clientId;
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $e->server->getSwooleServer()->close($clientId);
            $e->stopPropagation();

            return;
        }
        // 上下文创建
        $requestContext = RequestContext::getContext();
        $requestContext['server'] = $server = $e->getTarget();
        $requestContext['clientId'] = $clientId;

        $imiReceiveData = new ReceiveData($clientId, $e->reactorId, $e->data);
        $requestContext['receiveData'] = $imiReceiveData;

        // 中间件
        $dispatcher = $server->getBean('TcpDispatcher');
        $dispatcher->dispatch($imiReceiveData);
    }
}
