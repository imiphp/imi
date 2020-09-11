<?php

namespace Imi\Pool;

use Imi\Bean\BeanFactory;
use Imi\Pool\Interfaces\IPool;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\RequestContext;

abstract class PoolManager
{
    /**
     * \池子数组.
     *
     * @var \Imi\Pool\Interfaces\IPool[]
     */
    protected static $pools = [];

    /**
     * 最后获取资源时间.
     *
     * @var array
     */
    protected static $lastGetResourceTime = [];

    /**
     * 增加对象名称.
     *
     * @param string                           $name
     * @param string                           $poolClassName
     * @param \Imi\Pool\Interfaces\IPoolConfig $config
     * @param array|null                       $resourceConfig
     *
     * @return void
     */
    public static function addName(string $name, string $poolClassName, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        static::$pools[$name] = $pool = BeanFactory::newInstance($poolClassName, $name, $config, $resourceConfig);
        $pool->open();
    }

    /**
     * 获取所有对象名称.
     *
     * @return void
     */
    public static function getNames()
    {
        return array_keys(static::$pools);
    }

    /**
     * 清空池子.
     *
     * @return void
     */
    public static function clearPools()
    {
        foreach (static::$pools as $pool)
        {
            $pool->close();
        }
        static::$pools = [];
    }

    /**
     * 连接池是否存在.
     */
    public static function exists(string $name)
    {
        return isset(static::$pools[$name]);
    }

    /**
     * 获取实例.
     *
     * @param string $name
     *
     * @return \Imi\Pool\Interfaces\IPool
     */
    public static function getInstance(string $name): IPool
    {
        $pools = &static::$pools;
        if (!isset($pools[$name]))
        {
            throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
        }

        return $pools[$name];
    }

    /**
     * 获取池子中的资源.
     *
     * @param string $name
     *
     * @return IPoolResource
     */
    public static function getResource(string $name)
    {
        $resource = static::getInstance($name)->getResource();

        if ($resource)
        {
            static::pushResourceToRequestContext($resource);
        }

        static::$lastGetResourceTime[$name] = microtime(true);

        return $resource;
    }

    /**
     * 获取请求上下文资源，一个请求上下文通过此方法，只能获取同一个资源.
     *
     * @param string $name
     *
     * @return IPoolResource|null
     */
    public static function getRequestContextResource(string $name)
    {
        $requestContext = RequestContext::getContext();
        $resource = $requestContext['poolResource.' . $name] ?? null;
        if (null !== $resource && microtime(true) - static::$lastGetResourceTime[$name] > $resource->getPool()->getConfig()->getRequestResourceCheckInterval() && !$resource->checkState())
        {
            $resource->getPool()->release($resource);
            $resource = null;
        }
        if (null === $resource)
        {
            $resource = static::getResource($name);
            $requestContext['poolResource.' . $name] = $resource;
        }

        return $resource;
    }

    /**
     * 尝试获取资源，获取到则返回资源，没有获取到返回false.
     *
     * @param string $name
     *
     * @return IPoolResource|bool
     */
    public static function tryGetResource(string $name)
    {
        $resource = static::getInstance($name)->tryGetResource();
        if ($resource)
        {
            static::pushResourceToRequestContext($resource);
        }

        return $resource;
    }

    /**
     * 释放资源占用.
     *
     * @param string        $name
     * @param IPoolResource $resource
     *
     * @return void
     */
    public static function releaseResource(IPoolResource $resource)
    {
        $resource->getPool()->release($resource);
        static::removeResourceFromRequestContext($resource);
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放
     * 回调有两个参数：$resource(资源对象), $instance(操作实例对象，如数据库、Redis等)
     * 本方法返回值为回调的返回值
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return mixed
     */
    public static function use(string $name, callable $callback)
    {
        $resource = static::getResource($name);
        $result = null;
        try
        {
            $result = $callback($resource, $resource->getInstance());
        }
        finally
        {
            static::releaseResource($resource);
        }

        return $result;
    }

    /**
     * 释放当前上下文请求的未被释放的资源.
     *
     * @return void
     */
    public static function destroyCurrentContext()
    {
        $requestContext = RequestContext::getContext();
        $poolResources = $requestContext['poolResources'] ?? [];
        foreach ($poolResources as $resource)
        {
            $resource->getPool()->release($resource);
        }
        $requestContext['poolResources'] = [];
    }

    /**
     * 请求上下文中是否存在资源.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasRequestContextResource(string $name)
    {
        $resource = RequestContext::get('poolResource.' . $name);

        return null !== $resource;
    }

    /**
     * 把资源存放到当前上下文.
     *
     * @param IPoolResource $resource
     *
     * @return void
     */
    private static function pushResourceToRequestContext(IPoolResource $resource)
    {
        $requestContext = RequestContext::getContext();
        $poolResources = $requestContext['poolResources'] ?? [];
        $instance = $resource->getInstance();
        $poolResources[spl_object_hash($instance)] = $resource;
        $requestContext['poolResources'] = $poolResources;
    }

    /**
     * 把资源从当前上下文删除.
     *
     * @param IPoolResource $resource
     *
     * @return void
     */
    private static function removeResourceFromRequestContext(IPoolResource $resource)
    {
        $requestContext = RequestContext::getContext();
        $poolResources = $requestContext['poolResources'] ?? [];
        $instance = $resource->getInstance();
        $key = spl_object_hash($instance);
        if (isset($poolResources[$key]))
        {
            unset($poolResources[$key]);
            $requestContext['poolResources'] = $poolResources;
        }

        $name = 'poolResource.' . $resource->getPool()->getName();
        $poolResource = RequestContext::get($name);
        if ($poolResource === $resource)
        {
            $requestContext[$name] = null;
        }
    }

    /**
     * 清理连接池，只允许留下指定连接池.
     *
     * @param string[] $allowList
     *
     * @return void
     */
    public static function cleanAllow(array $allowList)
    {
        foreach (self::getNames() as $poolName)
        {
            if (!\in_array($poolName, $allowList))
            {
                self::getInstance($poolName)->close();
            }
        }
    }

    /**
     * 清理连接池，只允许留下指定连接池.
     *
     * @param string[] $denyList
     *
     * @return void
     */
    public static function cleanDeny(array $denyList)
    {
        foreach (self::getNames() as $poolName)
        {
            if (\in_array($poolName, $denyList))
            {
                self::getInstance($poolName)->close();
            }
        }
    }
}
