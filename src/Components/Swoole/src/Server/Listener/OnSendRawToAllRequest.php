<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Server\Server;

/**
 * 发送给所有 Worker 进程的连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendRawToAllRequest")
 */
class OnSendRawToAllRequest implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        $workerId = $data['workerId'];
        $data = $data['data'];
        $result = Server::sendRawToAll($data['data'], $data['serverName'], false);
        Server::sendMessage('sendRawToAllResponse', [
            'messageId' => $data['messageId'],
            'result'    => $result,
        ], $workerId);
    }
}
