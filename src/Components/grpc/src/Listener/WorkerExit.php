<?php

declare(strict_types=1);

namespace Imi\Grpc\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Grpc\Client\GrpcClient;
use Imi\Pool\PoolManager;
use Imi\Process\Event\ProcessEvents;
use Imi\Rpc\Client\Pool\RpcClientCoroutinePool;
use Imi\Rpc\Client\Pool\RpcClientSyncPool;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Util\Coroutine;

#[Listener(eventName: SwooleEvents::SERVER_WORKER_EXIT, priority: \Imi\Util\ImiPriority::IMI_MIN)]
#[Listener(eventName: ProcessEvents::PROCESS_END, priority: -19940311)]
class WorkerExit implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $inCo = Coroutine::isIn();
        foreach (PoolManager::getNames() as $name)
        {
            $pool = PoolManager::getInstance($name);
            if (($pool instanceof RpcClientSyncPool || $pool instanceof RpcClientCoroutinePool) && (GrpcClient::class === ($pool->getResourceConfig()[0]['clientClass'] ?? null)))
            {
                if ($inCo)
                {
                    $pool->close();
                }
                else
                {
                    Coroutine::create(static function () use ($pool): void {
                        $pool->close();
                    });
                }
            }
        }
    }
}
