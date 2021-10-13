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
 * 关闭指定标识-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.closeByFlagRequest")
 */
class OnCloseByFlagRequest implements IEventListener
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
        $result = Server::closeByFlag($data['flag'], $serverName, false);
        if (($data['needResponse'] ?? true) && !SwooleWorker::isWorkerIdProcess($workerId))
        {
            Server::sendMessage('closeByFlagResponse', [
                'messageId'  => $data['messageId'],
                'result'     => $result,
                'serverName' => $serverName,
            ], $workerId, $serverName);
        }
    }
}
