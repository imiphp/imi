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
    public function __construct(\Imi\Pool\Interfaces\IPool $pool, /**
     * 客户端对象
     */
    private readonly ?Client $client)
    {
        parent::__construct($pool);
    }

    /**
     * {@inheritDoc}
     */
    public function open(?callable $callback = null): bool
    {
        return $this->client->getClient()->connect();
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->client->getClient()->close();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance()
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
        return $this->client->getClient()->isConnected();
    }

    /**
     * {@inheritDoc}
     */
    public function isOpened(): bool
    {
        return $this->client->getClient()->isConnected();
    }
}
