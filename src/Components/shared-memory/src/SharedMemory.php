<?php

declare(strict_types=1);

namespace Imi\SharedMemory;

use Imi\Config;
use Imi\Pool\PoolManager;
use Imi\RequestContext;

abstract class SharedMemory
{
    /**
     * 获取新的内存共享连接实例.
     *
     * @param string $poolName 连接池名称
     *
     * @return \Imi\SharedMemory\Client
     */
    public static function getNewInstance($poolName = null): Client
    {
        return PoolManager::getResource($poolName ?? static::getDefaultPoolName())->getInstance();
    }

    /**
     * 获取内存共享连接实例，每个RequestContext中共用一个.
     *
     * @param string $poolName 连接池名称
     *
     * @return \Imi\SharedMemory\Client
     */
    public static function getInstance($poolName = null): Client
    {
        return PoolManager::getRequestContextResource($poolName ?? static::getDefaultPoolName())->getInstance();
    }

    /**
     * 释放内存共享连接实例.
     *
     * @param \Imi\SharedMemory\Client $client
     */
    public static function release(Client $client): void
    {
        $resource = RequestContext::get('poolResources.' . spl_object_id($client));
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放
     * 回调有 1 个参数：$instance(操作实例对象)
     * 本方法返回值为回调的返回值
     *
     * @return mixed
     */
    public static function use(string $objectName, callable $callable, ?string $poolName = null)
    {
        return PoolManager::use($poolName ?? static::getDefaultPoolName(), function ($resource, Client $client) use ($objectName, $callable) {
            $object = $client->getObject($objectName);

            return $callable($object);
        });
    }

    /**
     * 获取默认连接池名.
     */
    public static function getDefaultPoolName(): string
    {
        return Config::get('@app.sharedMemory.defaultPool', 'sharedMemory');
    }
}
