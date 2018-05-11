<?php
namespace Imi\Util;

abstract class ArrayUtil
{
	/**
	 * 从数组中移除一个元素
	 * @param array $array
	 * @param mixed $value
	 * @return array
	 */
	public static function remove($array, $value)
	{
		$index = array_search($array, $value);
		if(false !== $index)
		{
			return array_splice($array, $index, 1);
		}
		else
		{
			return $array;
		}
	}
}