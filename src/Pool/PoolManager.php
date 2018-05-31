<?php
namespace Imi\Pool;

use Imi\Pool\Interfaces\IPool;
use Imi\App;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Util\Call;

class PoolManager
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
		static::$pools[$name] = App::getBean($poolClassName, $config, $resourceConfig);
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
	 * @return IPoolResource
	 */
	public static function getResource(string $name): IPoolResource
	{
		return static::getInstance($name)->getResource();
	}

	/**
	 * 尝试获取资源，获取到则返回资源，没有获取到返回false
	 * @param string $name
	 * @return IPoolResource|boolean
	 */
	public static function tryGetResource(string $name)
	{
		return static::getInstance($name)->tryGetResource();
	}

	/**
	 * 释放资源占用
	 * @param string $name
	 * @param IPoolResource $resource
	 * @return void
	 */
	public static function releaseResource(string $name, IPoolResource $resource)
	{
		return static::getInstance($name)->release($resource);
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
		$result = Call::callUserFunc($callback, $resource, $resource->getInstance());
		$resource->release();
		return $result;
	}
}