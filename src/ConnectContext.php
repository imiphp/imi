<?php
namespace Imi;

use Imi\RequestContext;

abstract class ConnectContext
{
	private static $context = [];

	/**
	 * 为当前请求创建上下文
	 * @return void
	 */
	public static function create()
	{
		$key = static::getContextKey();
		if(!isset(static::$context[$key]))
		{
			static::$context[$key] = [];
		}
	}

	/**
	 * 销毁当前请求的上下文
	 * @return void
	 */
	public static function destroy($fd)
	{
		$key = static::getContextKey();
		if(isset(static::$context[$key]))
		{
			unset(static::$context[$key]);
		}
	}

	/**
	 * 判断当前请求上下文是否存在
	 * @return boolean
	 */
	public static function exsits()
	{
		if(RequestContext::exsits())
		{
			$key = static::getContextKey();
			return isset(static::$context[$key]);
		}
		else
		{
			return false;
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
		$key = static::getContextKey();
		return static::$context[$key][$name] ?? $default;
	}

	/**
	 * 设置上下文数据
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public static function set($name, $value)
	{
		$key = static::getContextKey();
		static::$context[$key][$name] = $value;
	}

	/**
	 * 获取当前上下文
	 * @return array
	 */
	public static function getContext()
	{
		$key = static::getContextKey();
		return static::$context[$key] ?? null;
	}

	/**
	 * 获取上下文的key
	 *
	 * @return string
	 */
	private static function getContextKey()
	{
		return RequestContext::getServer()->getName() . '-' . RequestContext::get('fd');
	}
}