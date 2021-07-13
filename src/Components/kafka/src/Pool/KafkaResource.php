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
    /**
     * Kafka 生产者.
     */
    private Producer $connection;

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, Producer $connection)
    {
        parent::__construct($pool);
        $this->connection = $connection;
    }

    /**
     * 打开
     */
    public function open(): bool
    {
        return true;
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        $this->connection->close();
    }

    /**
     * 获取对象实例.
     */
    public function getInstance(): Producer
    {
        return $this->connection;
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
        return true;
    }
}
