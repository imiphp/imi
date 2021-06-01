<?php

declare(strict_types=1);

namespace Imi\Grpc\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Grpc\Client\GrpcClient;
use Imi\Pool\PoolManager;
use Imi\Rpc\Client\Pool\RpcClientCoroutinePool;
use Imi\Rpc\Client\Pool\RpcClientSyncPool;
use Imi\Swoole\Util\Coroutine;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.EXIT",priority=Imi\Util\ImiPriority::IMI_MIN)
 * @Listener("IMI.PROCESS.END",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class WorkerExit implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        foreach (PoolManager::getNames() as $name)
        {
            $pool = PoolManager::getInstance($name);
            $inCo = Coroutine::isIn();
            if (($pool instanceof RpcClientSyncPool || $pool instanceof RpcClientCoroutinePool) && (GrpcClient::class === $pool->getResourceConfig()[0]['clientClass'] ?? null))
            {
                if ($inCo)
                {
                    $pool->close();
                }
                else
                {
                    go(function () use ($pool) {
                        $pool->close();
                    });
                }
            }
        }
    }
}
