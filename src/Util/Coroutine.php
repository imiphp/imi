<?php
namespace Imi\Util;

abstract class Coroutine extends \Swoole\Coroutine
{
	/**
	 * 是否在协程中
	 * @return boolean
	 */
	public static function isIn()
	{
		return static::getuid() > -1;
	}
}