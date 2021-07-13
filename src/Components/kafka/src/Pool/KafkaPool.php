<?php

declare(strict_types=1);

namespace Imi\Kafka\Pool;

use Imi\App;
use Imi\Pool\PoolManager;
use Imi\RequestContext;
use longlang\phpkafka\Consumer\Consumer;
use longlang\phpkafka\Consumer\ConsumerConfig;
use longlang\phpkafka\Producer\Producer;
use longlang\phpkafka\Producer\ProducerConfig;

/**
 * Kafka 客户端连接池.
 */
class KafkaPool
{
    /**
     * 获取新的连接实例.
     */
    public static function getNewInstance(?string $poolName = null): Producer
    {
        return PoolManager::getResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 获取连接实例，每个RequestContext中共用一个.
     */
    public static function getInstance(?string $poolName = null): Producer
    {
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 释放连接实例.
     *
     * @return void
     */
    public static function release(Producer $client)
    {
        $resource = RequestContext::get('poolResources.' . spl_object_hash($client));
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 处理连接池名称.
     *
     * @param string $poolName
     *
     * @return string
     */
    public static function parsePoolName(?string $poolName = null)
    {
        if (null === $poolName)
        {
            $poolName = static::getDefaultPoolName();
        }

        return $poolName;
    }

    /**
     * 获取默认池子名称.
     */
    public static function getDefaultPoolName(): string
    {
        return App::getBean('Kafka')->getDefaultPoolName();
    }

    public static function createConsumerConfig(array $config = []): ConsumerConfig
    {
        $clientId = getmypid() . '-' . uniqid('', true);
        if (!isset($config['clientId']))
        {
            $config['clientId'] = $clientId;
        }
        if (!isset($config['groupInstanceId']))
        {
            $config['groupInstanceId'] = $clientId;
        }
        if (!isset($config['maxWait']))
        {
            $config['maxWait'] = 10;
        }

        return new ConsumerConfig($config);
    }

    public static function createProducerConfig(array $config = []): ProducerConfig
    {
        $clientId = getmypid() . '-' . uniqid('', true);
        if (!isset($config['clientId']))
        {
            $config['clientId'] = $clientId;
        }
        if (!isset($config['groupInstanceId']))
        {
            $config['groupInstanceId'] = $clientId;
        }

        return new ProducerConfig($config);
    }

    /**
     * 使用连接池配置创建消费者.
     *
     * @param string|array|null $topic
     */
    public static function createConsumer(?string $poolName = null, $topic = null, array $config = []): Consumer
    {
        /** @var KafkaCoroutinePool|KafkaSyncPool $pool */
        $pool = PoolManager::getInstance(self::parsePoolName($poolName));

        return $pool->createConsumer($topic, $config);
    }
}
