<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Pool\PoolManager;
use Imi\Swoole\Pool\Model\PoolInfo;
use Imi\Swoole\Server\Server;
use Imi\Worker;

/**
 * 获取所有连接池信息-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.getPoolInfosRequest")
 */
class GetPoolInfosRequest implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $eData = $e->getData();
        $workerId = $eData['workerId'] ?? -1;
        $data = $eData['data'];
        $infos = [];
        $currentWorkerId = Worker::getWorkerId();
        foreach (PoolManager::getNames() as $poolName)
        {
            $pool = PoolManager::getInstance($poolName);
            $infos[] = new PoolInfo([
                'name'     => $poolName,
                'workerId' => $currentWorkerId,
                'count'    => $pool->getCount(),
                'used'     => $pool->getUsed(),
                'free'     => $pool->getFree(),
            ]);
        }

        Server::sendMessage('getPoolInfosResponse', [
            'messageId' => $data['messageId'],
            'workerId'  => $currentWorkerId,
            'infos'     => $infos,
        ], $workerId);
    }
}
