<?php
namespace Imi\Util;

abstract class Text
{
	/**
	 * 检查规则是否匹配，支持通配符*
	 * @param string $rule
	 * @param string $string
	 * @return void
	 */
	public static function checkRuleMatch($rule, $string)
	{
		$rule = '/^' . \str_replace('\\*', '.*', \preg_quote($rule)) . '$/';
		return \preg_match($rule, $string) > 0;
	}

	/**
	 * 检查类和方法是否匹配，支持通配符*
	 * @param string $rule
	 * @param string $className
	 * @param string $methodName
	 * @return void
	 */
	public static function checkClassMethodRule($rule, $className, $methodName)
	{
		list($classRule, $methodRule) = explode('::', $rule, 2);
		return static::checkRuleMatch($classRule, $className) && static::checkRuleMatch($methodRule, $methodName);
	}

	/**
	 * 检查类是否匹配，支持通配符*
	 * @param string $rule
	 * @param string $className
	 * @return void
	 */
	public static function checkClassRule($rule, $className)
	{
		list($classRule, ) = explode('::', $rule, 2);
		return static::checkRuleMatch($classRule, $className);
	}

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
}