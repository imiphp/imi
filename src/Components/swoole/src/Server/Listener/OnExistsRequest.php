<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Server\Server;
use Imi\Swoole\SwooleWorker;

/**
 * 连接是否存在-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.existsRequest")
 */
class OnExistsRequest implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $eData = $e->getData();
        $workerId = $eData['workerId'] ?? -1;
        $data = $eData['data'];
        $result = false;
        if (isset($data['clientIds']))
        {
            foreach ($data['clientIds'] as $clientId)
            {
                if ($result = Server::exists($clientId, $data['serverName'], false))
                {
                    break;
                }
            }
        }
        elseif (isset($data['clientId']))
        {
            $result = Server::exists($data['clientId'], $data['serverName'], false);
        }
        if (($data['needResponse'] ?? true) && !SwooleWorker::isWorkerIdProcess($workerId))
        {
            Server::sendMessage('existsResponse', [
                'messageId'  => $data['messageId'],
                'result'     => $result,
                'serverName' => $data['serverName'],
            ], $workerId, $data['serverName']);
        }
    }
}
