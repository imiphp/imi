<?php

declare(strict_types=1);

namespace Imi\Pool;

use Imi\App;
use Imi\Config;
use Imi\Pool\Interfaces\IPool;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\RequestContext;

class PoolManager
{
    /**
     * \池子数组.
     *
     * @var \Imi\Pool\Interfaces\IPool[]
     */
    protected static array $pools = [];

    /**
     * 最后获取资源时间.
     */
    protected static array $lastGetResourceTime = [];

    /**
     * 是否初始化.
     */
    protected static bool $inited = false;

    private function __construct()
    {
    }

    public static function init(): void
    {
        foreach (Config::getAliases() as $alias)
        {
            foreach (Config::get($alias . '.pools', []) as $poolName => $poolConfig)
            {
                $poolPool = $poolConfig['pool'];
                self::addName($poolName, $poolPool['class'], new PoolConfig($poolPool['config']), $poolConfig['resource']);
            }
        }
        self::$inited = true;
    }

    /**
     * 增加对象名称.
     *
     * @param \Imi\Pool\Interfaces\IPoolConfig $config
     * @param array|null                       $resourceConfig
     */
    public static function addName(string $name, string $poolClassName, Interfaces\IPoolConfig $config = null, $resourceConfig = null): void
    {
        static::$pools[$name] = $pool = App::getBean($poolClassName, $name, $config, $resourceConfig);
        $pool->open();
    }

    /**
     * 获取所有对象名称.
     *
     * @return string[]
     */
    public static function getNames(): array
    {
        return array_keys(static::$pools);
    }

    /**
     * 清空池子.
     */
    public static function clearPools(): void
    {
        if (static::$pools)
        {
            foreach (static::$pools as $pool)
            {
                $pool->close();
            }
            static::$pools = [];
        }
    }

    /**
     * 连接池是否存在.
     */
    public static function exists(string $name): bool
    {
        if (!self::$inited)
        {
            self::init();
        }

        return isset(static::$pools[$name]);
    }

    /**
     * 获取实例.
     */
    public static function getInstance(string $name): IPool
    {
        $pools = &static::$pools;
        if (!isset($pools[$name]))
        {
            if (self::$inited)
            {
                throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
            }
            else
            {
                self::init();
                if (!isset($pools[$name]))
                {
                    throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
                }
            }
        }

        return $pools[$name];
    }

    /**
     * 获取池子中的资源.
     */
    public static function getResource(string $name): IPoolResource
    {
        $resource = static::getInstance($name)->getResource();

        static::pushResourceToRequestContext($resource);

        static::$lastGetResourceTime[$name] = microtime(true);

        return $resource;
    }

    /**
     * 获取请求上下文资源，一个请求上下文通过此方法，只能获取同一个资源.
     */
    public static function getRequestContextResource(string $name): IPoolResource
    {
        $requestContext = RequestContext::getContext();
        $resource = $requestContext['poolResource.' . $name] ?? null;
        if (null !== $resource)
        {
            $requestResourceCheckInterval = $resource->getPool()->getConfig()->getRequestResourceCheckInterval();
            if ($requestResourceCheckInterval > 0 && microtime(true) - static::$lastGetResourceTime[$name] > $requestResourceCheckInterval && !$resource->checkState())
            {
                $resource->getPool()->release($resource);
                $resource = null;
            }
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
     */
    public static function releaseResource(IPoolResource $resource): void
    {
        $resource->getPool()->release($resource);
        static::removeResourceFromRequestContext($resource);
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放
     * 回调有两个参数：$resource(资源对象), $instance(操作实例对象，如数据库、Redis等)
     * 本方法返回值为回调的返回值
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
     */
    public static function destroyCurrentContext(): void
    {
        $requestContext = RequestContext::getContext();
        $poolResources = $requestContext['poolResources'] ?? [];
        if ($poolResources)
        {
            foreach ($poolResources as $resource)
            {
                $resource->getPool()->release($resource);
            }
        }
        $requestContext['poolResources'] = [];
    }

    /**
     * 请求上下文中是否存在资源.
     */
    public static function hasRequestContextResource(string $name): bool
    {
        $resource = RequestContext::get('poolResource.' . $name);

        return null !== $resource;
    }

    /**
     * 把资源存放到当前上下文.
     */
    private static function pushResourceToRequestContext(IPoolResource $resource): void
    {
        $requestContext = RequestContext::getContext();
        $poolResources = $requestContext['poolResources'] ?? [];
        $instance = $resource->getInstance();
        $poolResources[spl_object_hash($instance)] = $resource;
        $requestContext['poolResources'] = $poolResources;
    }

    /**
     * 把资源从当前上下文删除.
     */
    private static function removeResourceFromRequestContext(IPoolResource $resource): void
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
     */
    public static function cleanAllow(array $allowList): void
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
     */
    public static function cleanDeny(array $denyList): void
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
