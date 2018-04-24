<?php
namespace Imi\Util;

/**
 * 单例模式
 */
trait TSingleton
{
	/**
	 * 实例对象
	 * @var mixed
	 */
	private static $instance;

	private function __construct()
	{
	}

	/**
	 * 获取单例对象
	 * @param array ...$args
	 * @return void
	 */
	public static function getInstance(...$args)
	{
		if(null === static::$instance)
		{
			static::$instance = new static(...$args);
		}
		return static::$instance;
	}
}