<?php

declare(strict_types=1);

namespace Imi\Rpc\Client\Pool;

use Imi\Pool\BasePoolResource;
use Imi\Rpc\Client\IRpcClient;

/**
 * RPC连接池的资源.
 */
class RpcClientResource extends BasePoolResource
{
    /**
     * rpcClient对象
     *
     * @var IRpcClient
     */
    private $client;

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, IRpcClient $client)
    {
        parent::__construct($pool);
        $this->client = $client;
    }

    /**
     * 打开
     */
    public function open(): bool
    {
        $this->client->open();

        return $this->client->isConnected();
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        $this->client->close();
    }

    /**
     * 获取对象实例.
     *
     * @return IRpcClient
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
        return $this->client->isConnected();
    }
}
