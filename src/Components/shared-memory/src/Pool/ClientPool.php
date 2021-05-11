<?php

namespace Imi\SharedMemory\Pool;

use Imi\Pool\BaseAsyncPool;
use Imi\SharedMemory\Client;
use Imi\Util\Imi;

/**
 * 共享内存客户端池.
 */
class ClientPool extends BaseAsyncPool
{
    /**
     * 创建资源.
     *
     * @return \Imi\Pool\Interfaces\IPoolResource
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
