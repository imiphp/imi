<?php

declare(strict_types=1);

namespace Imi\Kafka\Pool;

use Imi\Pool\BasePoolResource;
use longlang\phpkafka\Producer\Producer;

/**
 * Kafka 生产者连接池的资源.
 */
class KafkaResource extends BasePoolResource
{
    public function __construct(\Imi\Pool\Interfaces\IPool $pool,
        /**
         * Kafka 生产者.
         */
        private readonly Producer $connection)
    {
        parent::__construct($pool);
    }

    /**
     * {@inheritDoc}
     */
    public function open(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->connection->close();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance(): Producer
    {
        return $this->connection;
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
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isOpened(): bool
    {
        return true;
    }
}
