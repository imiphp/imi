<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Tcp\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\TcpServer\Message\ReceiveData;

/**
 * Receive事件前置处理.
 *
 * @Listener(eventName="IMI.WORKERMAN.SERVER.TCP.MESSAGE",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeReceive implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        ['clientId' => $clientId, 'data' => $data] = $e->getData();
        // 上下文创建
        $requestContext = RequestContext::getContext();
        $requestContext['server'] = $server = $e->getTarget();
        $requestContext['clientId'] = $clientId;

        $imiReceiveData = new ReceiveData($clientId, $data);
        $requestContext['receiveData'] = $imiReceiveData;

        // 中间件
        $dispatcher = $server->getBean('TcpDispatcher');
        $dispatcher->dispatch($imiReceiveData);
    }
}
