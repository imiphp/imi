<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Server;
use Imi\Swoole\SwooleWorker;

/**
 * 发送给指定连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendToGroupsRequest")
 */
class OnSendToGroupsRequest implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $eData = $e->getData();
        $workerId = $eData['workerId'] ?? -1;
        $data = $eData['data'];
        $serverName = $data['serverName'];
        RequestContext::set('server', ServerManager::getServer($serverName));
        $result = Server::sendRawToGroup($data['groups'], $data['data'], $serverName, false);
        if (($data['needResponse'] ?? true) && !SwooleWorker::isWorkerIdProcess($workerId))
        {
            Server::sendMessage('sendToGroupsResponse', [
                'messageId'  => $data['messageId'],
                'result'     => $result,
                'serverName' => $serverName,
            ], $workerId, $serverName);
        }
    }
}
