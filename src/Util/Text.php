<?php
namespace Imi\Util;

abstract class Text
{
	/**
	 * 字符串是否以另一个字符串开头
	 * @param string $string
	 * @param string $compare
	 * @return string
	 */
	public static function startwith($string, $compare)
	{
		return 0 === strpos($string, $compare);
	}

	/**
	 * 字符串是否以另一个字符串结尾
	 * @param string $string
	 * @param string $compare
	 * @return string
	 */
	public static function endwith($string, $compare)
	{
		return substr($string, -strlen($compare)) === $compare;
	}

	/**
	 * 插入字符串
	 * @param string $string 原字符串
	 * @param int $position 位置
	 * @param string $insertString 被插入的字符串
	 * @return string
	 */
	public static function insert($string, $position, $insertString)
	{
		return substr_replace($string, $insertString, $position, 0);
	}

	/**
	 * 字符串是否为空字符串或者为null
	 * @param string $string
	 * @return boolean
	 */
	public static function isEmpty($string)
	{
		return '' === $string || null === $string;
	}
}