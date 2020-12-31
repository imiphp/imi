<?php

declare(strict_types=1);

namespace Imi\Redis;

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
        $resource = PoolManager::getResource(static::parsePoolName($poolName));
        $redis = $resource->getInstance();
        RequestContext::set('poolResources.' . spl_object_hash($redis), $resource);

        return $redis;
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
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 释放 Redis 连接实例.
     *
     * @param \Imi\Redis\RedisHandler $redis
     *
     * @return void
     */
    public static function release(RedisHandler $redis)
    {
        $resource = RequestContext::get('poolResources.' . spl_object_hash($redis));
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 处理连接池 名称.
     *
     * @param string|null $poolName
     *
     * @return string
     */
    public static function parsePoolName(?string $poolName = null): string
    {
        if (null === $poolName)
        {
            $poolName = static::getDefaultPoolName();
        }

        return $poolName;
    }

    /**
     * 获取默认池子名称.
     *
     * @return string
     */
    public static function getDefaultPoolName(): string
    {
        return Config::get('@currentServer.redis.defaultPool');
    }
}
