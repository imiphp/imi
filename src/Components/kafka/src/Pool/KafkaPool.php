<?php

declare(strict_types=1);

namespace Imi\Kafka\Pool;

use Imi\App;
use Imi\Config;
use Imi\Pool\PoolManager;
use Imi\RequestContext;
use Imi\Util\Imi;
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
     * 连接配置.
     */
    private static ?array $connections = null;

    /**
     * 获取新的连接实例.
     */
    public static function getNewInstance(?string $poolName = null): Producer
    {
        $poolName = static::parsePoolName($poolName);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getResource($poolName)->getInstance();
        }
        else
        {
            $config = Config::get('@app.kafka.connections.' . $poolName);
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found db config %s', $poolName));
            }

            $producerConfig = self::createProducerConfig($config);

            return new Producer($producerConfig);
        }
    }

    /**
     * 获取连接实例，每个RequestContext中共用一个.
     */
    public static function getInstance(?string $poolName = null): Producer
    {
        $poolName = static::parsePoolName($poolName);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getRequestContextResource($poolName)->getInstance();
        }
        else
        {
            $requestContextKey = '__kafka.' . $poolName;
            $requestContext = RequestContext::getContext();
            if (isset($requestContext[$requestContextKey]))
            {
                return $requestContext[$requestContextKey];
            }
            if (null === self::$connections)
            {
                self::$connections = Config::get('@app.kafka.connections');
            }
            $config = self::$connections[$poolName] ?? null;
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found kafka config %s', $poolName));
            }
            /** @var Producer|null $connection */
            $connection = App::get($requestContextKey);
            if (null === $connection)
            {
                $producerConfig = self::createProducerConfig($config);
                $connection = new Producer($producerConfig);
                App::set($requestContextKey, $connection);
            }

            return $requestContext[$requestContextKey] = $connection;
        }
    }

    /**
     * 释放连接实例.
     *
     * @return void
     */
    public static function release(Producer $client)
    {
        $resource = RequestContext::get('poolResources.' . spl_object_id($client));
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
        // @phpstan-ignore-next-line
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
        if (Imi::checkAppType('swoole'))
        {
            /** @var KafkaCoroutinePool|KafkaSyncPool $pool */
            $pool = PoolManager::getInstance(self::parsePoolName($poolName));

            return $pool->createConsumer($topic, $config);
        }
        else
        {
            if (null === $poolName)
            {
                $poolName = self::parsePoolName($poolName);
            }
            $config = self::createConsumerConfig(array_merge(Config::get('@app.kafka.connections.' . $poolName), $config));
            if ($topic)
            {
                $config->setTopic($topic);
            }

            return new Consumer($config);
        }
    }
}
