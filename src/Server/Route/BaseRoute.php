<?php
namespace Imi\Server\Route;

use Imi\Server\Route\Annotation\Route as RouteAnnotation;

abstract class BaseRoute implements IRoute
{
	/**
	 * 路由规则
	 * url => \Imi\Server\Route\Annotation\Route[]
	 * @var array
	 */
	protected $rules = [];

	/**
	 * 增加路由规则
	 * @param string $url url规则
	 * @param mixed $callable 回调
	 * @param \Imi\Server\Route\Annotation\Route $annotation 路由定义注解，可选
	 * @return void
	 */
	public function addRule(string $url, $callable, \Imi\Server\Route\Annotation\Route $annotation = null)
	{
		if(null === $annotation)
		{
			$annotation = new \Imi\Server\Route\Annotation\Route([
				'url'	=>	$url,
			]);
		}
		$this->rules[$url][$this->hashKey($annotation)] = [
			'annotation'=>	$annotation,
			'callable'	=>	$callable,
		];
	}

	/**
	 * 增加路由规则，直接使用注解方式
	 * @param \Imi\Server\Route\Annotation\Route $annotation
	 * @param mixed $callable
	 * @return void
	 */
	public function addRuleAnnotation(\Imi\Server\Route\Annotation\Route $annotation = null, $callable)
	{
		$this->rules[$annotation->url][$this->hashKey($annotation)] = [
			'annotation'=>	$annotation,
			'callable'	=>	$callable,
		];
	}

	/**
	 * 清空路由规则
	 * @return void
	 */
	public function clearRules()
	{
		$this->rules = [];
	}

	/**
	 * 路由规则是否存在
	 * @param \Imi\Server\Route\Annotation\Route $rule
	 * @return boolean
	 */
	public function existsRule(RouteAnnotation $rule)
	{
		return isset($this->rules[$rule->url][$this->hashKey($rule)]);
	}

	/**
	 * 获取路由规则
	 * @return array
	 */
	public function getRules()
	{
		return $this->rules;
	}

	/**
	 * 对key做hash
	 * @param mixed $key
	 * @return boolean
	 */
	private function hashKey($key)
	{
		return md5(serialize($key));
	}
}