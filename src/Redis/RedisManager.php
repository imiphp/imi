<?php
namespace Imi\Redis;

use Imi\App;
use Imi\Config;
use Imi\Main\Helper;
use Imi\RequestContext;
use Imi\Pool\PoolManager;

abstract class RedisManager
{
    /**
     * 获取新的 Redis 连接实例
     * @param string $poolName 连接池名称
     * @return \Imi\Redis\RedisHandler
     */
    public static function getNewInstance($poolName = null)
    {
        $resource = PoolManager::getResource(static::parsePoolName($poolName));
        $redis = $resource->getInstance();
        RequestContext::set('poolResources.' . spl_object_hash($redis), $resource);
        return $redis;
    }

    /**
     * 获取 Redis 连接实例，每个RequestContext中共用一个
     * @param string $poolName 连接池名称
     * @return \Imi\Redis\RedisHandler|null
     */
    public static function getInstance($poolName = null)
    {
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 释放 Redis 连接实例
     * @param \Imi\Redis\RedisHandler $redis
     * @return void
     */
    public static function release($redis)
    {
        $resource = RequestContext::get('poolResources.' . spl_object_hash($redis));
        if(null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 处理连接池 名称
     *
     * @param string $poolName
     * @return string
     */
    public static function parsePoolName($poolName = null)
    {
        if(null === $poolName)
        {
            $poolName = static::getDefaultPoolName();
        }
        return $poolName;
    }

    /**
     * 获取默认池子名称
     * @return string
     */
    public static function getDefaultPoolName()
    {
        return Config::get('@currentServer.redis.defaultPool');
    }
}