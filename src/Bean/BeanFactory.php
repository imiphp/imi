<?php
namespace Imi\Bean;

use Imi\Config;
use Imi\Bean\Parser\BeanParser;
use Imi\RequestContext;

abstract class BeanFactory
{
	/**
	 * 实例化
	 * @param string $class
	 * @param mixed ...$args
	 * @return mixed
	 */
	public static function newInstance($class, ...$args)
	{
		$ref = new \ReflectionClass($class);
		$tpl = static::getTpl($ref);
		$object = eval($tpl);
		if($ref->hasMethod('__init'))
		{
			$ref->getMethod('__init')->invoke($object, ...$args);
		}
		return $object;
	}

	/**
	 * 获取类模版
	 * @param \ReflectionClass $ref
	 * @param mixed ...$args
	 * @return string
	 */
	private static function getTpl($ref)
	{
		$class = $ref->getName();
		$methodsTpl = static::getMethodsTpl($ref, $class);
		$construct = '';
		if(null !== $ref->getConstructor())
		{
			$construct = 'parent::__construct(...$args);';
		}
		// 匿名类
		$tpl = <<<TPL
return new class(...\$args) extends \\{$class}
{
	private \$beanProxy;

	public function __construct(...\$args)
	{
		{$construct}
		\$this->beanProxy = new \Imi\Bean\BeanProxy(\$this);
	}

{$methodsTpl}
};
TPL;
		return $tpl;
	}

	/**
	 * 获取方法模版
	 * @param \ReflectionClass $ref
	 * @param string $class
	 * @return string
	 */
	private static function getMethodsTpl($ref, $class)
	{
		$tpl = '';
		foreach($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
		{
			if($method->isStatic() || '__construct' === $method->name)
			{
				continue;
			}
			$paramsTpls = static::getMethodParamTpls($method);
			$methodReturnType = static::getMethodReturnType($method);
			$tpl .= <<<TPL
	public function {$method->name}({$paramsTpls['define']}){$methodReturnType}
	{
		\$args = [{$paramsTpls['args']}];{$paramsTpls['args_variadic']}
		return \$this->beanProxy->call(
			'{$method->name}',
			function({$paramsTpls['define']}){
				return parent::{$method->name}({$paramsTpls['call']});
			},
			\$args
		);
	}

TPL;
		}
		return $tpl;
	}

	/**
	 * 获取方法参数模版们
	 * @param \ReflectionClass $ref
	 * @return string
	 */
	private static function getMethodParamTpls(\ReflectionMethod $method)
	{
		$result = [
			'args'			=>	[],
			'define'		=>	[],
			'call'			=>	[],
		];
		foreach($method->getParameters() as $param)
		{
			// 数组参数，支持引用传参
			if(!$param->isVariadic())
			{
				$result['args'][] = static::getMethodParamArgsTpl($param);
			}
			// 方法参数定义
			$result['define'][] = static::getMethodParamDefineTpl($param);
			// 调用传参
			$result['call'][] = static::getMethodParamCallTpl($param);
		}
		foreach($result as &$item)
		{
			if(is_array($item))
			{
				$item = implode(',', $item);
			}
		}
		// 可变参数
		if(isset($param) && $param->isVariadic())
		{
			$result['args_variadic'] = static::getMethodArgsVariadicTpl($param);
		}
		else
		{
			$result['args_variadic'] = '';
		}
		return $result;
	}

	/**
	 * 获取方法参数模版
	 * @param \ReflectionParameter $param
	 * @return string
	 */
	private static function getMethodParamArgsTpl(\ReflectionParameter $param)
	{
		$reference = $param->isPassedByReference() ? '&' : '';
		return $reference . '$' . $param->name;
	}

	/**
	 * 获取方法参数定义模版
	 * @param \ReflectionParameter $param
	 * @return string
	 */
	private static function getMethodParamDefineTpl(\ReflectionParameter $param)
	{
		$result = '';
		// 类型
		$paramType = $param->getType();
		$result .= null === $paramType ? '' : ((string)$paramType . ' ');
		if($param->isPassedByReference())
		{
			// 引用传参
			$result .= '&';
		}
		else if($param->isVariadic())
		{
			// 可变参数...
			$result .= '...';
		}
		// $参数名
		$result .= '$' . $param->name;
		// 默认值
		if($param->isOptional() && !$param->isVariadic())
		{
			$result .= ' = ' . json_encode($param->getDefaultValue());
		}
		return $result;
	}

	/**
	 * 获取方法参数调用模版
	 * @param \ReflectionParameter $param
	 * @return string
	 */
	private static function getMethodParamCallTpl(\ReflectionParameter $param)
	{
		return ($param->isVariadic() ? '...' : '') . '$' . $param->name;
	}

	/**
	 * 获取方法可变参数模版
	 * @param \ReflectionParameter $param
	 * @return string
	 */
	private static function getMethodArgsVariadicTpl(\ReflectionParameter $param)
	{
		return <<<TPL

		foreach(\${$param->name} as \$item)
		{
			\$args[] = \$item;
		}
TPL;
	}

	/**
	 * 获取方法返回值模版
	 * @param \ReflectionMethod $method
	 * @return string
	 */
	private static function getMethodReturnType(\ReflectionMethod $method)
	{
		if(!$method->hasReturnType())
		{
			return '';
		}
		return ' : ' . $method->getReturnType();
	}

}