<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Tcp\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\TcpServer\Message\ReceiveData;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Server\Tcp\Event\WorkermanTcpMessageEvent;

/**
 * Receive事件前置处理.
 */
#[Listener(eventName: WorkermanEvents::SERVER_TCP_MESSAGE, priority: \Imi\Util\ImiPriority::IMI_MAX)]
class BeforeReceive implements IEventListener
{
    /**
     * @param WorkermanTcpMessageEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        // 上下文创建
        $requestContext = RequestContext::getContext();
        $requestContext['server'] = $server = $e->getTarget();
        $requestContext['clientId'] = $e->clientId;

        $imiReceiveData = new ReceiveData($e->clientId, $e->data);
        $requestContext['receiveData'] = $imiReceiveData;

        // 中间件
        $dispatcher = $server->getBean('TcpDispatcher');
        $dispatcher->dispatch($imiReceiveData);
    }
}
