<?php

declare(strict_types=1);

namespace Imi\SharedMemory\Pool;

use Imi\Pool\BasePoolResource;
use Imi\SharedMemory\Client;

/**
 * 共享内存客户端资源.
 */
class ClientResource extends BasePoolResource
{
    /**
     * 客户端对象
     */
    private Client $client;

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, Client $client)
    {
        parent::__construct($pool);
        $this->client = $client;
    }

    /**
     * 打开
     */
    public function open(?callable $callback = null): bool
    {
        return $this->client->getClient()->connect();
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        $this->client->getClient()->close();
    }

    /**
     * 获取对象实例.
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->client;
    }

    /**
     * 重置资源，当资源被使用后重置一些默认的设置.
     */
    public function reset(): void
    {
    }

    /**
     * 检查资源是否可用.
     */
    public function checkState(): bool
    {
        return $this->client->getClient()->isConnected();
    }
}
