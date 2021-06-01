<?php

declare(strict_types=1);

namespace Imi\Kafka\Pool;

use Imi\Bean\BeanFactory;
use Imi\Pool\BaseSyncPool;
use Imi\Pool\TUriResourceConfig;
use longlang\phpkafka\Producer\Producer;

/**
 * 同步 Kafka 客户端连接池.
 */
class KafkaSyncPool extends BaseSyncPool
{
    use TKafkaPool;
    use TUriResourceConfig;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * 创建资源.
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();
        $producerConfig = KafkaPool::createProducerConfig($config);
        $producer = new Producer($producerConfig);

        return BeanFactory::newInstance(KafkaResource::class, $this, $producer);
    }
}
