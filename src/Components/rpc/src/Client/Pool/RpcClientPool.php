<?php

declare(strict_types=1);

namespace Imi\Rpc\Client\Pool;

use Imi\Config;
use Imi\Pool\PoolManager;
use Imi\RequestContext;
use Imi\Rpc\Client\IRpcClient;
use Imi\Rpc\Client\IService;

/**
 * Rpc客户端连接池.
 */
class RpcClientPool
{
    /**
     * 获取新的数据库连接实例.
     *
     * @param string|null $poolName 连接池名称
     */
    public static function getNewInstance(?string $poolName = null): IRpcClient
    {
        return PoolManager::getResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 获取数据库连接实例，每个RequestContext中共用一个.
     *
     * @param string|null $poolName 连接池名称
     */
    public static function getInstance(?string $poolName = null): ?IRpcClient
    {
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName))->getInstance();
    }

    /**
     * 获取服务对象
     */
    public static function getService(string $serviceName, ?string $poolName = null): ?IService
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
     */
    public static function release(IRpcClient $client): void
    {
        $resource = RequestContext::get('poolResources.' . spl_object_hash($client));
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
        return Config::get('@currentServer.rpc.defaultPool');
    }
}
