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
 * 获取指定连接池信息-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.getPoolInfoRequest")
 */
class GetPoolInfoRequest implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $eData = $e->getData();
        $workerId = $eData['workerId'] ?? -1;
        $data = $eData['data'];
        $poolName = $data['poolName'];
        $pool = PoolManager::getInstance($poolName);
        $info = new PoolInfo([
            'name'     => $poolName,
            'workerId' => Worker::getWorkerId(),
            'count'    => $pool->getCount(),
            'used'     => $pool->getUsed(),
            'free'     => $pool->getFree(),
        ]);

        Server::sendMessage('getPoolInfoResponse', [
            'messageId' => $data['messageId'],
            'info'      => $info,
        ], $workerId);
    }
}
