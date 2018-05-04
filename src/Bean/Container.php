<?php
namespace Imi\Bean;

use Imi\Bean\BeanProxy;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\Parser\BeanParser;
use Imi\Bean\Proxy\Proxy;

class Container
{
	/**
	 * 单例对象们
	 * @var array
	 */
	public $singletonObjects = [];

	/**
	 * Bean处理器
	 * @var \Imi\Bean\Parser\BeanParser
	 */
	public $beanParser;

	public function __construct()
	{
		$this->beanParser = BeanParser::getInstance();
	}

	public function getBean($name, ...$params)
	{
		// 单例中有数据，且无实例化参数时直接返回单例
		if(isset($this->singletonObjects[$name]) && !isset($params[0]))
		{
			return $this->singletonObjects[$name];
		}
		$data = $this->beanParser->getData();
		
		if(isset($data[$name]))
		{
			$object = new $data[$name]['className'](...$params);
		}
		else if(class_exists($name))
		{
			$object = new $name(...$params);
		}
		else
		{
			throw new \Exception(sprintf('bean %s not found', $name));
		}

		// 代理类
		$object = new BeanProxy($object);

		// 传参实例化强制不使用单例
		if(!isset($params[0]) && isset($data[$name]['instanceType']) && $data[$name]['instanceType'] === Bean::INSTANCE_TYPE_SINGLETON)
		{
			$this->singletonObjects[$name] = $object;
		}
		return $object;
	}
}