<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Server;
use Workerman\Connection\TcpConnection;

/**
 * 发送给所有连接-请求
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
        ['data' => $data, 'serverName' => $serverName] = $data['data'];
        /** @var IWorkermanServer|null $server */
        $server = Server::getServer($serverName);
        if (!$server)
        {
            return;
        }
        /** @var TcpConnection $connection */
        foreach ($server->getWorker()->connections as $connection)
        {
            $connection->send($data);
        }
    }
}
