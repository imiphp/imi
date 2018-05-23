<?php
namespace Imi\Util;

abstract class Random
{
	/**
	 * 随机整数
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public function int($min = PHP_INT_MIN, $max = PHP_INT_MAX)
	{
		return mt_rand($min, $max);
	}

	/**
	 * 随机生成小数
	 * @param float $min
	 * @param float $max
	 * @param integer $precision 最大小数位数
	 * @return float
	 */
	public function number($min = PHP_INT_MIN, $max = PHP_INT_MAX, $precision = 2)
	{
		return round($min + mt_rand() / mt_getrandmax() * ($max - $min), $precision);
	}
}