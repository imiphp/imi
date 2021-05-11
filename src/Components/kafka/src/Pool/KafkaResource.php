<?php

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
     *
     * @var Producer
     */
    private $connection;

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, Producer $connection)
    {
        parent::__construct($pool);
        $this->connection = $connection;
    }

    /**
     * 打开
     *
     * @return bool
     */
    public function open()
    {
        return true;
    }

    /**
     * 关闭.
     *
     * @return void
     */
    public function close()
    {
        $this->connection->close();
    }

    /**
     * 获取对象实例.
     *
     * @return Producer
     */
    public function getInstance()
    {
        return $this->connection;
    }

    /**
     * 重置资源，当资源被使用后重置一些默认的设置.
     *
     * @return void
     */
    public function reset()
    {
    }

    /**
     * 检查资源是否可用.
     *
     * @return bool
     */
    public function checkState(): bool
    {
        return true;
    }
}
