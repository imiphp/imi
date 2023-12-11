<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Server\Event\PipeMessageEvent;
use Imi\Swoole\Server\Server;
use Imi\Swoole\SwooleWorker;

/**
 * 连接是否存在-请求
 */
#[Listener(eventName: 'imi.pipe_message.existsRequest')]
class OnExistsRequest implements IEventListener
{
    /**
     * @param PipeMessageEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $eData = $e->data;
        $workerId = $eData['workerId'] ?? -1;
        $data = $eData['data'];
        $serverName = $data['serverName'];
        $result = false;
        if (isset($data['flag']))
        {
            $result = Server::flagExists($data['flag'], $serverName, false);
        }
        elseif (isset($data['clientId']))
        {
            $result = Server::exists($data['clientId'], $serverName, false);
        }
        if (($data['needResponse'] ?? true) && !SwooleWorker::isWorkerIdProcess($workerId))
        {
            Server::sendMessage('existsResponse', [
                'messageId'  => $data['messageId'],
                'result'     => $result,
                'serverName' => $serverName,
            ], $workerId, $serverName);
        }
    }
}
