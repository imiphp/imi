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
     */
    private IRpcClient $client;

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, IRpcClient $client)
    {
        parent::__construct($pool);
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function open(): bool
    {
        return $this->client->open() && $this->client->isConnected();
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->client->close();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance(): IRpcClient
    {
        return $this->client;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function checkState(): bool
    {
        return $this->client->checkConnected();
    }

    /**
     * {@inheritDoc}
     */
    public function isOpened(): bool
    {
        return $this->client->isConnected();
    }
}
