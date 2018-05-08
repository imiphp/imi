<?php
namespace Imi\Bean;

use Imi\Util\Call;
use Imi\Util\Text;
use Imi\Aop\JoinPoint;
use Imi\Aop\AroundJoinPoint;
use Imi\Bean\Parser\AopParser;
use Imi\Aop\AfterThrowingJoinPoint;
use Imi\Aop\AfterReturningJoinPoint;
use Imi\Bean\Annotation\AfterThrowing;
use Imi\App;

class BeanProxy
{
	/**
	 * 对象
	 * @var mixed
	 */
	private $object;

	/**
	 * 对象反射
	 * @var \ReflectionClass
	 */
	private $refClass;

	/**
	 * 存储每个类对应的切面关系
	 * @var \SplPriorityQueue[]
	 */
	private static $aspects = [];

	public function __construct($object)
	{
		$this->object = $object;
		$this->init();
	}

	/**
	 * 魔术方法
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		try{
			// 先尝试环绕
			if($this->parseAround($method, $args, $result))
			{
				return $result;
			}
			else
			{
				// 正常请求
				return $this->call($method, $args);
			}
		}catch(\Throwable $throwable){
			// 异常
			$this->parseAfterThrowing($method, $args, $throwable);
		}
	}

	public function __set($name, $value) 
    {
		$this->object->$name = $value;
    }

    public function __get($name) 
    {
		return $this->object->$name;
    }

    public function __isset($name) 
    {
		return isset($this->object->$name);
    }

    public function __unset($name) 
    {
		unset($this->object->$name);
    }

	/**
	 * 初始化
	 * @return void
	 */
	private function init()
	{
		$this->refClass = new \ReflectionClass($this->object);
		// 属性注入
		$this->injectProps();
		// 初始化方法
		if($this->refClass->hasMethod('__init'))
		{
			$this->object->__init();
		}
		$className = $this->refClass->getName();
		// 每个类只需处理一次
		if(isset(static::$aspects[$className]))
		{
			return;
		}
		static::$aspects[$className] = new \SplPriorityQueue;
		$aopData = AopParser::getInstance()->getData();
		foreach($aopData as $aspectClassName => $option)
		{
			// 判断是否属于当前类的切面
			if($this->isAspectCurrentClass($option))
			{
				static::$aspects[$className]->insert($option, $option['aspect']->priority);
			}
		}
	}

	private function injectProps()
	{
		$className = $this->refClass->getName();
		$aopData = AopParser::getInstance()->getData();
		if(!isset($aopData[$className]))
		{
			return;
		}
		foreach($aopData[$className]['property'] as $propName => $option)
		{
			$this->object->$propName = App::getBean($option['inject']->name, $option['inject']->args);
		}
	}

	/**
	 * 判断是否属于当前类的切面
	 * @param array $option
	 * @return boolean
	 */
	private function isAspectCurrentClass($option)
	{
		if(!isset($option['method']))
		{
			return false;
		}
		foreach($option['method'] as $methodName => $methodOption)
		{
			if(!isset($methodOption['pointCut']))
			{
				continue;
			}
			foreach($methodOption['pointCut']->allow as $allowItem)
			{
				if(Text::checkClassRule($allowItem, $this->refClass->getName()))
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 正常请求
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	private function call($method, $args)
	{
		$this->parseBefore($method, $args);
		// 原始方法调用
		$result = $this->object->$method(...$args);
		$this->parseAfter($method, $args);
		$this->parseAfterReturning($method, $args, $result);
		return $result;
	}

	/**
	 * 处理前置
	 * @param string $method
	 * @param array $args
	 * @return void
	 */
	private function parseBefore($method, $args)
	{
		$this->doAspect($method, 'before', function($aspectClassName, $methodName) use($method, $args){
			$joinPoint = new JoinPoint('before', $method, $args, $this->object, $this);
			Call::callUserFunc([new $aspectClassName, $methodName], $joinPoint);
		});
	}

	/**
	 * 处理后置
	 * @param string $method
	 * @param array $args
	 * @return void
	 */
	private function parseAfter($method, $args)
	{
		$this->doAspect($method, 'after', function($aspectClassName, $methodName) use($method, $args){
			$joinPoint = new JoinPoint('after', $method, $args, $this->object, $this);
			Call::callUserFunc([new $aspectClassName, $methodName], $joinPoint);
		});
	}

	/**
	 * 处理返回值
	 * @param string $method
	 * @param array $args
	 * @param mixed $returnValue
	 * @return void
	 */
	private function parseAfterReturning($method, $args, &$returnValue)
	{
		$this->doAspect($method, 'afterReturning', function($aspectClassName, $methodName) use($method, $args, &$returnValue){
			$joinPoint = new AfterReturningJoinPoint('afterReturning', $method, $args, $this->object, $this);
			$joinPoint->setReturnValue($returnValue);
			Call::callUserFunc([new $aspectClassName, $methodName], $joinPoint);
			$returnValue = $joinPoint->getReturnValue();
		});
	}

	/**
	 * 处理环绕
	 * @param string $method
	 * @param array $args
	 * @param mixed $returnValue
	 * @return void
	 */
	private function parseAround($method, $args, &$returnValue)
	{
		$aroundAspectDoList = [];
		$this->doAspect($method, 'around', function($aspectClassName, $methodName) use(&$aroundAspectDoList){
			$aroundAspectDoList[] = [new $aspectClassName, $methodName];
		});
		if(!isset($aroundAspectDoList[0]))
		{
			return false;
		}
		$aroundAspectDoList = array_reverse($aroundAspectDoList);

		$nextJoinPoint = null;
		$nextAroundAspectDo = null;

		foreach($aroundAspectDoList as $aroundAspectDo)
		{
			$joinPoint = new AroundJoinPoint('around', $method, $args, $this->object, $this, (null === $nextJoinPoint ? function() use($method, $args){
				return $this->call($method, $args);
			} : function() use($nextAroundAspectDo, $nextJoinPoint){
				return Call::callUserFunc($nextAroundAspectDo, $nextJoinPoint);
			}));
			$nextJoinPoint = $joinPoint;
			$nextAroundAspectDo = $aroundAspectDo;
		}
		$returnValue = Call::callUserFunc($nextAroundAspectDo, $nextJoinPoint);
		return true;
	}

	/**
	 * 处理异常
	 * @param string $method
	 * @param array $args
	 * @param \Throwable $throwable
	 * @return void
	 */
	private function parseAfterThrowing($method, $args, \Throwable $throwable)
	{
		$isCancelThrow = false;
		$this->doAspect($method, 'afterThrowing', function($aspectClassName, $methodName, AfterThrowing $annotation) use($method, $args, $throwable, &$isCancelThrow){
			// 验证异常是否捕获
			if(isset($annotation->allow[0]) || isset($annotation->deny[0]))
			{
				$throwableClassName = get_class($throwable);
				if(isset($annotation->allow[0]))
				{
					$allowResult = false;
					foreach($annotation->allow as $rule)
					{
						$allowResult = Text::checkRuleMatch($rule, $throwableClassName);
						if($allowResult)
						{
							break;
						}
					}
					if(!$allowResult)
					{
						return;
					}
				}
				$denyResult = false;
				foreach($annotation->deny as $rule)
				{
					$denyResult = Text::checkRuleMatch($rule, $throwableClassName);
					if($denyResult)
					{
						return;
					}
				}
			}
			// 处理
			$joinPoint = new AfterThrowingJoinPoint('afterThrowing', $method, $args, $this->object, $this, $throwable);
			Call::callUserFunc([new $aspectClassName, $methodName], $joinPoint);
			if(!$isCancelThrow && $joinPoint->isCancelThrow())
			{
				$isCancelThrow = true;
			}
		});
		// 不取消依旧抛出
		if(!$isCancelThrow)
		{
			throw $throwable;
		}
	}

	/**
	 * 执行切面操作
	 * @param string $method 方法名
	 * @param string $pointType 切入点类型
	 * @param callable $callback 回调
	 * @return void
	 */
	private function doAspect($method, $pointType, $callback)
	{
		$className = $this->refClass->getName();
		$list = clone static::$aspects[$className];
		foreach($list as $option)
		{
			$aspectClassName = $option['className'];
			foreach($option['method'] as $methodName => $methodOption)
			{
				if(!isset($methodOption[$pointType]) || !$methodOption[$pointType])
				{
					continue;
				}
				$allowResult = false;
				foreach($methodOption['pointCut']->allow as $rule)
				{
					$allowResult = Text::checkClassMethodRule($rule, $className, $method);
					if($allowResult)
					{
						break;
					}
				}
				if($allowResult)
				{
					$denyResult = false;
					foreach($methodOption['pointCut']->deny as $rule)
					{
						$denyResult = Text::checkClassMethodRule($rule, $className, $method);
						if($denyResult)
						{
							break;
						}
					}
					if($denyResult)
					{
						continue;
					}
					Call::callUserFunc($callback, $aspectClassName, $methodName, $methodOption[$pointType]);
				}
			}
		}
	}
}