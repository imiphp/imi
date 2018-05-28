<?php
namespace Imi\Util;

/**
 * 位操作工具
 */
abstract class Bit
{
	/**
	 * 判断是否包含值
	 * @param int $value
	 * @param int $subValue
	 * @return boolean
	 */
	public static function has($value, $subValue)
	{
		return $subValue === $value & $subValue;
	}
}