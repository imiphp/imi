<?php

declare(strict_types=1);

namespace Imi\SharedMemory\Pool;

use Imi\SharedMemory\Client;
use Imi\Swoole\Pool\BaseAsyncPool;
use Imi\Util\Imi;

/**
 * 共享内存客户端池.
 */
class ClientPool extends BaseAsyncPool
{
    /**
     * {@inheritDoc}
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();
        if (!isset($config['socketFile']))
        {
            $config['socketFile'] = Imi::getRuntimePath('imi-shared-memory.sock');
        }
        if (empty($config['storeTypes']))
        {
            $config['storeTypes'] = [
                \Yurun\Swoole\SharedMemory\Client\Store\KV::class,
                \Yurun\Swoole\SharedMemory\Client\Store\Stack::class,
                \Yurun\Swoole\SharedMemory\Client\Store\Queue::class,
                \Yurun\Swoole\SharedMemory\Client\Store\PriorityQueue::class,
            ];
        }

        return new ClientResource($this, new Client($config));
    }
}
