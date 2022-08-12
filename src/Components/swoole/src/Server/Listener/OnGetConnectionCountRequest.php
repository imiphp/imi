<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Server;

/**
 * 获取当前连接数量-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.getConnectionCountRequest")
 */
class OnGetConnectionCountRequest implements IEventListener
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
        if ($data['needResponse'] ?? true)
        {
            Server::sendMessage('getConnectionCountResponse', [
                'messageId'  => $data['messageId'],
                'result'     => iterator_count(Server::getServer($serverName, ISwooleServer::class)->getSwoolePort()->connections),
                'serverName' => $serverName,
            ], $workerId, $serverName);
        }
    }
}
