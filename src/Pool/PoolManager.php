<?php
namespace Imi\Pool;

use Imi\App;
use Imi\RequestContext;
use Imi\Bean\BeanFactory;
use Imi\Pool\Interfaces\IPool;
use Imi\Pool\Interfaces\IPoolResource;

abstract class PoolManager
{
    /**
     * \池子数组
     * @var \Imi\Pool\Interfaces\IPool[]
     */
    protected static $pools = [];
    
    /**
     * 增加对象名称
     * @param string $name
     * @param \Imi\Pool\Interfaces\IPoolConfig $config
     * @param [type] $resourceConfig
     * @return void
     */
    public static function addName(string $name, string $poolClassName, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        static::$pools[$name] = BeanFactory::newInstance($poolClassName, $name, $config, $resourceConfig);
        static::$pools[$name]->open();
    }

    /**
     * 获取所有对象名称
     * @return void
     */
    public static function getNames()
    {
        return array_keys(static::$pools);
    }

    /**
     * 清空池子
     * @return void
     */
    public static function clearPools()
    {
        foreach(static::$pools as $pool)
        {
            $pool->close();
        }
        static::$pools = [];
    }

    /**
     * 获取实例
     * @param string $name
     * @return \Swoole\Atomic
     */
    public static function getInstance(string $name): IPool
    {
        if(!isset(static::$pools[$name]))
        {
            throw new \RuntimeException(sprintf('getInstance failed, %s is not found', $name));
        }
        return static::$pools[$name];
    }

    /**
     * 获取池子中的资源
     * @param string $name
     * @return IPoolResource|null
     */
    public static function getResource(string $name)
    {
        $resource = static::getInstance($name)->getResource();

        if(RequestContext::exsits())
        {
            static::pushResourceToRequestContext($resource);
        }

        return $resource;
    }

    /**
     * 获取请求上下文资源，一个请求上下文通过此方法，只能获取同一个资源
     * @param string $name
     * @return IPoolResource|null
     */
    public static function getRequestContextResource(string $name)
    {
        $resource = RequestContext::get('poolResource.' . $name);
        if(null === $resource)
        {
            $resource = static::getResource($name);
            RequestContext::set('poolResource.' . $name, $resource);
        }
        return $resource;
    }

    /**
     * 尝试获取资源，获取到则返回资源，没有获取到返回false
     * @param string $name
     * @return IPoolResource|boolean
     */
    public static function tryGetResource(string $name)
    {
        $resource = static::getInstance($name)->tryGetResource();
        if(!$resource)
        {
            static::pushResourceToRequestContext($resource);
        }
        return $resource;
    }

    /**
     * 释放资源占用
     * @param string $name
     * @param IPoolResource $resource
     * @return void
     */
    public static function releaseResource(IPoolResource $resource)
    {
        $resource->getPool()->release($resource);
        if(RequestContext::exsits())
        {
            static::removeResourceFromRequestContext($resource);
        }
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放
     * 回调有两个参数：$resource(资源对象), $instance(操作实例对象，如数据库、Redis等)
     * 本方法返回值为回调的返回值
     * @param string $name
     * @param callable $callback
     * @return mixed
     */
    public static function use(string $name, callable $callback)
    {
        $resource = static::getResource($name);
        $result = null;
        try{
            $result = call_user_func($callback, $resource, $resource->getInstance());
        }
        finally{
            static::releaseResource($resource);
        }
        return $result;
    }

    /**
     * 释放当前上下文请求的未被释放的资源
     * @return void
     */
    public static function destroyCurrentContext()
    {
        $poolResources = RequestContext::get('poolResources', []);
        foreach($poolResources as $resource)
        {
            $resource->getPool()->release($resource);
        }
        RequestContext::set('poolResources', []);
    }

    /**
     * 把资源存放到当前上下文
     *
     * @param IPoolResource $resource
     * @return void
     */
    private static function pushResourceToRequestContext(IPoolResource $resource)
    {
        $poolResources = RequestContext::get('poolResources', []);
        $instance = $resource->getInstance();
        $poolResources[spl_object_hash($instance)] = $resource;
        RequestContext::set('poolResources', $poolResources);
    }

    /**
     * 把资源从当前上下文删除
     * @param IPoolResource $resource
     * @return void
     */
    private static function removeResourceFromRequestContext(IPoolResource $resource)
    {
        $poolResources = RequestContext::get('poolResources', []);
        $instance = $resource->getInstance();
        $key = spl_object_hash($instance);
        if(isset($poolResources[$key]))
        {
            unset($poolResources[$key]);
            RequestContext::set('poolResources', $poolResources);
        }

        $name = 'poolResource.' . $resource->getPool()->getName();
        $poolResource = RequestContext::get($name);
        if($poolResource === $resource)
        {
            RequestContext::set($name, null);
        }
    }
}