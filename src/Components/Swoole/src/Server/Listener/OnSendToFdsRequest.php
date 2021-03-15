<?php

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Server\Server;

/**
 * 发送给指定连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendToFdsRequest")
 */
class OnSendToFdsRequest implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        $workerId = $data['workerId'];
        $data = $data['data'];
        $result = Server::sendRaw($data['data'], $data['fds'], $data['serverName'], false);
        Server::sendMessage('sendToFdsResponse', [
            'messageId' => $data['messageId'],
            'result'    => $result,
        ], $workerId);
    }
}
