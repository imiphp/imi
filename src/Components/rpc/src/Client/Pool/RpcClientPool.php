<?php

namespace Imi\Rpc\Client\Pool;

use Imi\Config;
use Imi\Pool\PoolManager;
use Imi\RequestContext;

/**
 * Rpc客户端连接池.
 */
class RpcClientPool
{
    /**
     * 获取新的数据库连接实例.
     *
     * @param string $poolName 连接池名称
     *
     * @return \Imi\Rpc\Client\IRpcClient
     */
    public static function getNewInstance($poolName = null)
    {
        return PoolManager::getResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 获取数据库连接实例，每个RequestContext中共用一个.
     *
     * @param string $poolName 连接池名称
     *
     * @return \Imi\Rpc\Client\IRpcClient|null
     */
    public static function getInstance($poolName = null)
    {
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 获取服务对象
     *
     * @param string $serviceName
     * @param string $poolName
     *
     * @return \Imi\Rpc\Client\IService|null
     */
    public static function getService($serviceName, $poolName = null)
    {
        $client = static::getInstance($poolName);
        if ($client)
        {
            return $client->getService($serviceName);
        }

        return null;
    }

    /**
     * 释放数据库连接实例.
     *
     * @param \Imi\Rpc\Client\IRpcClient $client
     *
     * @return void
     */
    public static function release($client)
    {
        $resource = RequestContext::get('poolResources.' . spl_object_hash($client));
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 处理连接池 名称.
     *
     * @param string $poolName
     *
     * @return string
     */
    public static function parsePoolName($poolName = null)
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
    public static function getDefaultPoolName()
    {
        return Config::get('@currentServer.rpc.defaultPool');
    }
}
