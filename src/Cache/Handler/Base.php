<?php
namespace Imi\Cache\Handler;

use Imi\RequestContext;
use Psr\SimpleCache\CacheInterface;

abstract class Base implements CacheInterface
{
	/**
	 * 数据读写格式化处理器
	 * 为null时不做任何处理
	 * @var string
	 */
	protected $formatHandlerClass;

	public function __construct($option = [])
	{
		foreach($option as $k => $v)
		{
			$this->$k = $v;
		}
	}

	/**
	 * 写入编码
	 * @param mixed $data
	 * @return mixed
	 */
	protected function encode($data)
	{
		if(null === $this->formatHandlerClass)
		{
			return $data;
		}
		else
		{
			return RequestContext::getBean($this->formatHandlerClass)->encode($data);
		}
	}

	/**
	 * 读出解码
	 * @param mixed $data
	 * @return mixed
	 */
	protected function decode($data)
	{
		if(null === $this->formatHandlerClass)
		{
			return $data;
		}
		else
		{
			return RequestContext::getBean($this->formatHandlerClass)->decode($data);
		}
	}
}