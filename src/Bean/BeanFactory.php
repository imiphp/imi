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
		$tpl = static::getTpl($class);
		return static::injectProperties(eval($tpl), $class);
	}

	/**
	 * 获取类模版
	 * @param string $class
	 * @param mixed ...$args
	 * @return string
	 */
	private static function getTpl($class)
	{
		$ref = new \ReflectionClass($class);
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
	 * 获取加随机符号的类名
	 * @param string $class
	 * @return string
	 */
	private static function getClassName($class)
	{
		return str_replace('\\', '_', $class) . '_' . str_replace('.', '', uniqid('', true));
	}

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

	private static function getMethodParamArgsTpl(\ReflectionParameter $param)
	{
		$reference = $param->isPassedByReference() ? '&' : '';
		return $reference . '$' . $param->name;
	}

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

	private static function getMethodParamCallTpl(\ReflectionParameter $param)
	{
		return ($param->isVariadic() ? '...' : '') . '$' . $param->name;
	}

	private static function getMethodArgsVariadicTpl(\ReflectionParameter $param)
	{
		return <<<TPL

		foreach(\${$param->name} as \$item)
		{
			\$args[] = \$item;
		}
TPL;
	}

	private static function getMethodReturnType(\ReflectionMethod $method)
	{
		if(!$method->hasReturnType())
		{
			return '';
		}
		return ' : ' . $method->getReturnType();
	}

	/**
	 * 注入属性
	 * @param object $object
	 * @param string $class
	 * @return object
	 */
	private static function injectProperties($object, $class)
	{
		$ref = new \ReflectionClass($class);
		$beanData = BeanParser::getInstance()->getData();
		if(!isset($beanData[$class]['beanName']))
		{
			return $object;
		}
		// 优先从服务器bean配置获取
		try{
			$request = RequestContext::get('request');
			if(null !== $request)
			{
				$beanProperties = Config::get('@server_' . $request->getServerInstance()->getName() . '.beans.' . $beanData[$class]['beanName'], null);
			}
		}
		catch(\Throwable $ex)
		{
			$beanProperties = null;
		}
		// 全局bean配置
		if(null === $beanProperties)
		{
			$beanProperties = Config::get('beans.' . $beanData[$class]['beanName'], []);
		}
		foreach($beanProperties as $name => $value)
		{
			$propRef = $ref->getProperty($name);
			if(null === $propRef)
			{
				continue;
			}
			$propRef->setAccessible(true);
			$propRef->setValue($object, $value);
		}
		return $object;
	}
}