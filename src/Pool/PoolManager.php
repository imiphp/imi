<?php
namespace Imi\Pool;

use Imi\Pool\Interfaces\IPool;
use Imi\App;

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
}