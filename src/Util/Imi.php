<?php
namespace Imi\Util;

/**
 * 框架里杂七杂八的各种工具方法
 */
abstract class Imi
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
	 * 检查验证比较规则集
	 * @param string|array $rules
	 * @param callable $valueCallback
	 * @return boolean
	 */
	public static function checkCompareRules($rules, $valueCallback)
	{
		foreach(is_array($rules) ? $rules : [$rules] as $fieldName => $rule)
		{
			if(is_numeric($fieldName))
			{
				if(!static::checkCompareRule($rule, $valueCallback))
				{
					return false;
				}
			}
			else if(preg_match('/^' . $rule . '$/', Call::callUserFunc($valueCallback, $fieldName)) <= 0)
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * 检查验证比较规则，如果符合规则返回bool，不符合规则返回null
	 * id=1
	 * id!=1 id<>1
	 * id
	 * !id
	 * @param string $rule
	 * @param callable $valueCallback
	 * @return boolean
	 */
	public static function checkCompareRule($rule, $valueCallback)
	{
		if(isset($rule[0]) && '!' === $rule[0])
		{
			// 不应该存在参数支持
			return null === Call::callUserFunc($valueCallback, substr($rule, 1));
		}
		else if(preg_match('/([^!<=]+)(!=|<>|=)(.+)/', $rule, $matches) > 0)
		{
			$value = Call::callUserFunc($valueCallback, $matches[1]);
			switch($matches[2])
			{
				case '!=':
				case '<>':
					return null !== $value && $value != $matches[3];
				case '=':
					return $value == $matches[3];
				default:
					return false;
			}
		}
		else
		{
			return null !== Call::callUserFunc($valueCallback, $rule);
		}
	}

	/**
	 * 处理按.分隔的规则文本，支持\.转义不分隔
	 * @param string $rule
	 */
	public static function parseDotRule($rule)
	{
		$result = preg_split('#(?<!\\\)\.#', $rule);
		array_walk($result, function(&$value, $key){
			if(false !== strpos($value,'\.'))
			{
				$value = str_replace('\.', '.', $value);
			}
		});
		return $result;
	}
}