<?php
namespace Imi\Redis;

use Imi\App;
use Imi\Main\Helper;
use Imi\RequestContext;
use Imi\Pool\PoolManager;

abstract class RedisManager
{
    /**
     * 获取新的数据库连接实例
     * @param string $poolName 连接池名称
     * @return \Swoole\Coroutine\Redis
     */
    public static function getNewInstance($poolName = null)
    {
        return PoolManager::getResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 获取数据库连接实例，每个RequestContext中共用一个
     * @param string $poolName 连接池名称
     * @return \Swoole\Coroutine\Redis|null
     */
    public static function getInstance($poolName = null)
    {
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 释放数据库连接实例
     * @param \Swoole\Coroutine\Redis $redis
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
     * @param int $queryType
     * @return string
     */
    private static function parsePoolName($poolName = null)
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
        $namespace = null;
        if(RequestContext::exsits())
        {
            try{
                $namespace = RequestContext::getServer()->getConfig()['namespace'];
                $defaultPool = Helper::getMain($namespace)->getConfig()['redis']['defaultPool'] ?? null;
                if(null === $defaultPool)
                {
                    $namespace = null;
                }
                else
                {
                    return $defaultPool;
                }
            }
            catch(\Throwable $ex)
            {
                $namespace = null;
            }
        }
        if(null === $namespace)
        {
            $namespace = App::getNamespace();
        }
        $poolName = Helper::getMain($namespace)->getConfig()['redis']['defaultPool'] ?? null;
        return $poolName;
    }
}