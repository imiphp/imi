<?php
namespace Imi\Bean;

use Imi\Config;
use Imi\Worker;
use Imi\Util\File;
use Imi\RequestContext;
use Imi\Util\ClassObject;
use Imi\Bean\Parser\BeanParser;

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

		$cacheFileName = static::getCacheFileName($class);
		if(!is_file($cacheFileName))
		{
			$tpl = static::getTpl($ref);
			$path = dirname($cacheFileName);
			if(!is_dir($path))
			{
				File::createDir($path);
			}
			File::writeFile($cacheFileName, '<?php ' . $tpl);
		}

		$object = include $cacheFileName;

		if(method_exists($object, '__init'))
		{
			$object->__init(...$args);
		}
		return $object;
	}

	/**
	 * 获取类缓存文件名
	 *
	 * @param string $className
	 * @return string
	 */
	private static function getCacheFileName($className)
	{
		$path = Config::get('@app.beanClassCache', sys_get_temp_dir());
		return File::path($path, 'imiBeanCache', Worker::getWorkerID() ?? 'imi', str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php');
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
		$constructMethod = $ref->getConstructor();
		if(null !== $constructMethod)
		{
			$paramsTpls = static::getMethodParamTpls($constructMethod);
			$constructDefine = $paramsTpls['define'];
			$construct = "parent::__construct({$paramsTpls['call']});";
		}
		else
		{
			$constructDefine = '...$args';
		}
		// 匿名类模版定义
		$tpl = <<<TPL
return new class(...\$args) extends \\{$class}
{
	private \$beanProxy;

	public function __construct({$constructDefine})
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
			if($method->isStatic() || '__construct' === $method->name || $method->isFinal())
			{
				continue;
			}
			$paramsTpls = static::getMethodParamTpls($method);
			$methodReturnType = static::getMethodReturnType($method);
			$tpl .= <<<TPL
	public function {$method->name}({$paramsTpls['define']}){$methodReturnType}
	{
		\$__args__ = [{$paramsTpls['args']}];{$paramsTpls['args_variadic']}
		return \$this->beanProxy->call(
			'{$method->name}',
			function({$paramsTpls['define']}){
				return parent::{$method->name}({$paramsTpls['call']});
			},
			\$__args__
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
		// 调用如果参数为空处理
		if('' === $result['call'])
		{
			$result['call'] = '...func_get_args()';
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
		$result['args_variadic'] .= <<<STR

		if(!isset(\$__args__[func_num_args() - 1]))
		{
			\$__allArgs__ = func_get_args();
			\$__args__ = array_merge(\$__args__, array_splice(\$__allArgs__, count(\$__args__)));
		}
STR;
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
			if($param->isDefaultValueAvailable())
			{
				$result .= ' = ' . json_encode($param->getDefaultValue(), JSON_UNESCAPED_SLASHES);
			}
			else
			{
				$result .= ' = null';
			}
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

		foreach(\${$param->name} as \$__item__)
		{
			\$__args__[] = \$__item__;
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

	/**
	 * 获取对象类名
	 * @param string|object $object
	 * @return string
	 */
	public static function getObjectClass($object)
	{
		if(is_object($object))
		{
			if(ClassObject::isAnymous($object))
			{
				return get_parent_class($object);
			}
			else
			{
				return get_class($object);
			}
		}
		else
		{
			return (string)$object;
		}
	}
}