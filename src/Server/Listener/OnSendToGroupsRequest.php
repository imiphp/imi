<?php

namespace Imi\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\Server;

/**
 * 发送给指定连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendToGroupsRequest")
 */
class OnSendToGroupsRequest implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        $data = $e->getData();
        $workerId = $data['workerID'];
        $data = $data['data'];
        $result = Server::sendRawToGroup($data['groups'], $data['data'], $data['serverName'], false);
        Server::sendMessage('sendToGroupsResponse', [
            'messageId' => $data['messageId'],
            'result'    => $result,
        ], $workerId);
    }
}
