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
	public static function create($fd)
	{
		if(!RequestContext::exsits())
		{
			RequestContext::create();
		}
		RequestContext::set('fd', $fd);
		if(!isset(static::$context[$fd]))
		{
			static::$context[$fd] = [];
		}
	}

	/**
	 * 销毁当前请求的上下文
	 * @return void
	 */
	public static function destroy($fd)
	{
		if(isset(static::$context[$fd]))
		{
			unset(static::$context[$fd]);
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
			return isset(static::$context[RequestContext::get('fd')]);
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
		return static::$context[RequestContext::get('fd')][$name] ?? $default;
	}

	/**
	 * 设置上下文数据
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public static function set($name, $value)
	{
		static::$context[RequestContext::get('fd')][$name] = $value;
	}

	/**
	 * 获取当前上下文
	 * @return array
	 */
	public static function getContext()
	{
		return static::$context[RequestContext::get('fd')] ?? null;
	}
}