<?php

declare(strict_types=1);

namespace Imi\Redis;

use Imi\App;
use Imi\Config;
use Imi\Pool\PoolManager;
use Imi\RequestContext;

class RedisManager
{
    private function __construct()
    {
    }

    /**
     * 获取新的 Redis 连接实例.
     *
     * @param string|null $poolName 连接池名称
     *
     * @return \Imi\Redis\RedisHandler
     */
    public static function getNewInstance(?string $poolName = null): RedisHandler
    {
        $poolName = static::parsePoolName($poolName);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getResource($poolName)->getInstance();
        }
        else
        {
            $config = Config::get('@app.redis.connections.' . $poolName);
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found redis config %s', $poolName));
            }

            $class = $config['handlerClass'] ?? \Redis::class;
            /** @var RedisHandler $redis */
            $redis = App::getBean(RedisHandler::class, new $class());
            self::initRedisConnection($redis, $config);

            return $redis;
        }
    }

    /**
     * 获取 Redis 连接实例，每个RequestContext中共用一个.
     *
     * @param string|null $poolName 连接池名称
     *
     * @return \Imi\Redis\RedisHandler|null
     */
    public static function getInstance(?string $poolName = null): ?RedisHandler
    {
        $poolName = static::parsePoolName($poolName);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getRequestContextResource($poolName)->getInstance();
        }
        else
        {
            $requestContextKey = '__redis.' . $poolName;
            $context = RequestContext::getContext();
            $redis = $context[$requestContextKey] ?? null;
            if (null === $redis)
            {
                $config = Config::get('@app.redis.connections.' . $poolName);
                if (null === $config)
                {
                    throw new \RuntimeException(sprintf('Not found redis config %s', $poolName));
                }

                $class = $config['handlerClass'] ?? \Redis::class;
                /** @var RedisHandler $redis */
                $redis = $context[$requestContextKey] = App::getBean(RedisHandler::class, new $class());
                self::initRedisConnection($redis, $config);
            }

            return $redis;
        }
    }

    /**
     * 释放 Redis 连接实例.
     *
     * @param \Imi\Redis\RedisHandler $redis
     */
    public static function release(RedisHandler $redis): void
    {
        $resource = RequestContext::get('poolResources')[spl_object_hash($redis)] ?? null;
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 处理连接池 名称.
     */
    public static function parsePoolName(?string $poolName = null): string
    {
        if (null === $poolName || '' === $poolName)
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
        return Config::get('@currentServer.redis.defaultPool');
    }

    /**
     * 初始化 Redis 连接.
     */
    public static function initRedisConnection(RedisHandler $redis, array $config): void
    {
        if (!$redis->isCluster())
        {
            $redis->connect($config['host'] ?? '127.0.0.1', $config['port'] ?? 6379, $config['timeout'] ?? 0);
            if (('' !== ($config['password'] ?? '')) && !$redis->auth($config['password']))
            {
                throw new \RedisException('Redis auth failed');
            }
            if (isset($config['db']) && !$redis->select($config['db']))
            {
                throw new \RedisException('Redis select db failed');
            }
        }
        $options = $config['options'] ?? [];
        if (($config['serialize'] ?? true) && !isset($options[\Redis::OPT_SERIALIZER]))
        {
            $options[\Redis::OPT_SERIALIZER] = \Redis::SERIALIZER_PHP;
        }
        if ($options)
        {
            foreach ($options as $key => $value)
            {
                if (!$redis->setOption($key, $value))
                {
                    throw new \RuntimeException(sprintf('Redis setOption %s=%s failed', $key, $value));
                }
            }
        }
    }
}
