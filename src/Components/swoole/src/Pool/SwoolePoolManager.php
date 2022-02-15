<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool;

use Imi\App;
use Imi\Pool\PoolManager;
use Imi\Swoole\Pool\Model\PoolInfo;
use Imi\Swoole\Server\Util\LocalServerUtil;
use Imi\Swoole\SwooleWorker;
use Imi\Swoole\Util\Co\ChannelContainer;

class SwoolePoolManager extends PoolManager
{
    protected static int $atomic = 0;

    /**
     * 获取连接池信息.
     *
     * @return PoolInfo[]
     */
    public static function getInfo(string $poolName, float $timeout = 10): array
    {
        /** @var LocalServerUtil $localServerUtil */
        $localServerUtil = App::getBean(LocalServerUtil::class);
        $workerIds = range(0, SwooleWorker::getWorkerNum() + SwooleWorker::getTaskWorkerNum() - 1);
        $id = 'SwoolePoolManager:' . (++self::$atomic);
        $channel = ChannelContainer::getChannel($id);
        $localServerUtil->sendMessage('getPoolInfoRequest', [
            'messageId' => $id,
            'poolName'  => $poolName,
        ], $workerIds);
        $result = [];
        foreach ($workerIds as $_)
        {
            $popResult = $channel->pop($timeout);
            if (false === $popResult)
            {
                break;
            }
            $info = new PoolInfo($popResult['info']);
            $result[$info->getWorkerId()] = $info;
        }
        ksort($result);

        return $result;
    }

    /**
     * 获取所有连接池信息.
     *
     * @return PoolInfo[][]
     */
    public static function getInfos(float $timeout = 10): array
    {
        /** @var LocalServerUtil $localServerUtil */
        $localServerUtil = App::getBean(LocalServerUtil::class);
        $workerIds = range(0, SwooleWorker::getWorkerNum() + SwooleWorker::getTaskWorkerNum() - 1);
        $id = 'SwoolePoolManager:' . (++self::$atomic);
        $channel = ChannelContainer::getChannel($id);
        $localServerUtil->sendMessage('getPoolInfosRequest', [
            'messageId' => $id,
        ], $workerIds);
        $result = [];
        foreach ($workerIds as $_)
        {
            $popResult = $channel->pop($timeout);
            if (false === $popResult)
            {
                break;
            }
            $workerId = $popResult['workerId'];
            $list = [];
            foreach ($popResult['infos'] as $info)
            {
                $list[] = new PoolInfo($info);
            }
            $result[$workerId] = $list;
        }
        ksort($result);

        return $result;
    }
}
