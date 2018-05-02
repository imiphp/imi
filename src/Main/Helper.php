<?php
namespace Imi\Main;

/**
 * 主类帮助类
 */
abstract class Helper
{
	public static $mains = [];

	/**
	 * 获取主类实例对象
	 * @param string $namespace
	 * @return \Imi\Main\BaseMain
	 */
	public static function getMain($namespace)
	{
		if(isset(static::$mains[$namespace]))
		{
			return static::$mains[$namespace];
		}
		$className = $namespace . '\\Main';
		if(class_exists($className))
		{
			static::$mains[$namespace] = new $className;
			return static::$mains[$namespace];
		}
		else
		{
			return null;
		}
	}
}