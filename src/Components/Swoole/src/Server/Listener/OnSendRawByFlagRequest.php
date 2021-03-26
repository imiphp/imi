<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Server\Server;

/**
 * 发送给指定标识-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendRawByFlagRequest")
 */
class OnSendRawByFlagRequest implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $eData = $e->getData();
        $data = $eData['data'];
        $result = Server::sendRawByFlag($data['data'], $data['flag'], $data['serverName'], false);
        if ($data['needResponse'] ?? true)
        {
            Server::sendMessage('sendRawByFlagResponse', [
                'messageId' => $data['messageId'],
                'result'    => $result,
            ], $eData['workerId']);
        }
    }
}
