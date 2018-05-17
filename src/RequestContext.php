<?php
namespace Imi;

use Imi\Util\Coroutine;

abstract class RequestContext
{
	private static $context = [];

	/**
	 * 为当前协程创建上下文
	 * @return void
	 */
	public static function create()
	{
		if(!Coroutine::isIn())
		{
			throw new \RuntimeException('Create context failed, is not in coroutine');
		}
		$coID = Coroutine::getuid();
		if(!isset(static::$context[$coID]))
		{
			static::$context[$coID] = [];
		}
		else
		{
			throw new \RuntimeException('Create context failed, cannot create a duplicate context');
		}
	}

	/**
	 * 销毁当前协程的上下文
	 * @return void
	 */
	public static function destroy()
	{
		$coID = Coroutine::getuid();
		if(isset(static::$context[$coID]))
		{
			unset(static::$context[$coID]);
		}
		else
		{
			throw new \RuntimeException('Destroy context failed, current context is not found');
		}
	}

	/**
	 * 获取上下文数据
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($name, $default = null)
	{
		$coID = Coroutine::getuid();
		if(!isset(static::$context[$coID]))
		{
			throw new \RuntimeException('get context data failed, current context is not found');
		}
		if(isset(static::$context[$coID][$name]))
		{
			return static::$context[$coID][$name];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * 设置上下文数据
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public static function set($name, $value)
	{
		$coID = Coroutine::getuid();
		if(!isset(static::$context[$coID]))
		{
			throw new \RuntimeException('set context data failed, current context is not found');
		}
		static::$context[$coID][$name] = $value;
	}

	/**
	 * 获取当前上下文
	 * @return array
	 */
	public static function getContext()
	{
		$coID = Coroutine::getuid();
		if(!isset(static::$context[$coID]))
		{
			throw new \RuntimeException('get context failed, current context is not found');
		}
		return static::$context[$coID];
	}
}