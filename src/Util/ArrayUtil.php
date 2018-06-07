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

	/**
	 * 多维数组递归合并
	 * @param array ...$arrays
	 * @return array
	 */
	public static function recursiveMerge(...$arrays)
	{
		$merged = array ();
		foreach($arrays as $array)
		{
			if (!is_array($array))
			{
				continue;
			}
			foreach ( $array as $key => $value )
			{
				if (is_string ( $key ))
				{
					if (is_array ( $value ) && isset($merged[$key]) && is_array ( $merged [$key] ))
					{
						$merged [$key] = static::recursiveMerge ( $merged [$key], $value );
					}
					else
					{
						$merged [$key] = $value;
					}
				}
				else
				{
					$merged [] = $value;
				}
			}
		}
		return $merged;
	}

	/**
	 * 将二维数组第二纬某key变为一维的key
	 * @param array $array 原数组
	 * @param string $column 列名
	 * @param boolean $keepOld 是否保留列名，默认保留
	 */
	public static function columnToKey(&$array, $column, $keepOld = true)
	{
		$s = count($array);
		for($i = 0; $i < $s; ++$i)
		{
			$array[$array[$i][$column]] = $array[$i];
			if(!$keepOld)
			{
				unset($array[$i]);
			}
		}
	}

	/**
	 * 判断数组是否为关联数组
	 * @param array $array 
	 * @return bool 
	 */
	public static function isAssoc($array)
	{  
		return array_keys($array) !== range(0, count($array) - 1);  
	}
}