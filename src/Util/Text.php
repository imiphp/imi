<?php
namespace Imi\Util;

abstract class Text
{
	public static function checkRuleMatch($rule, $string)
	{
		$rule = '/^' . \str_replace('\\*', '.*', \preg_quote($rule)) . '$/';
		return \preg_match($rule, $string) > 0;
	}

	public static function checkClassMethodRule($rule, $className, $methodName)
	{
		list($classRule, $methodRule) = explode('::', $rule, 2);
		return static::checkRuleMatch($classRule, $className) && static::checkRuleMatch($methodRule, $methodName);
	}

	public static function checkClassRule($rule, $className)
	{
		list($classRule, ) = explode('::', $rule, 2);
		return static::checkRuleMatch($classRule, $className);
	}
}