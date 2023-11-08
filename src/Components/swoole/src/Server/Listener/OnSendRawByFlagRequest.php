<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Server\Server;
use Imi\Swoole\SwooleWorker;

/**
 * 发送给指定标识-请求
 */
#[Listener(eventName: 'IMI.PIPE_MESSAGE.sendRawByFlagRequest')]
class OnSendRawByFlagRequest implements IEventListener
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
        $result = Server::sendRawByFlag($data['data'], $data['flag'], $serverName, false);
        if (($data['needResponse'] ?? true) && !SwooleWorker::isWorkerIdProcess($workerId))
        {
            Server::sendMessage('sendRawByFlagResponse', [
                'messageId'  => $data['messageId'],
                'result'     => $result,
                'serverName' => $serverName,
            ], $workerId, $serverName);
        }
    }
}
