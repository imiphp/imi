<?php
namespace Imi\Server\Session;

use Imi\RequestContext;

abstract class Session
{
	/**
	 * 设置Session值
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public static function set($name, $value)
	{
		RequestContext::getBean('SessionManager')->set($name, $value);
	}

	/**
	 * 获取Session值
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($name = null, $default = null)
	{
		return RequestContext::getBean('SessionManager')->get($name, $default);
	}

	/**
	 * 删除Session值
	 * @param string $name
	 * @return void
	 */
	public static function delete($name)
	{
		RequestContext::getBean('SessionManager')->delete($name);
	}

	/**
	 * 获取一次值后将该值删除，可用于验证码等一次性功能
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public static function once($name, $default = false)
	{
		return RequestContext::getBean('SessionManager')->once($name, $default);
	}

	/**
	 * 清空所有Session
	 * @param string $name
	 * @return void
	 */
	public static function clear()
	{
		RequestContext::getBean('SessionManager')->clear();
	}
}