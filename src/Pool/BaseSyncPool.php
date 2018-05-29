<?php
namespace Imi\Pool;

use Swoole\Channel;
use Imi\Pool\Interfaces\IPoolResource;

/**
 * 同步池子，支持使用在大部分情况
 * 仅支持尝试获取后立即返回结果，如果获取失败返回null
 * 支持跨进程连接池管理
 */
abstract class BaseSyncPool extends BasePool
{
	/**
	 * 队列
	 * @var \Swoole\Coroutine\Channel
	 */
	protected $queue;

	public function __init()
	{
		parent::__init();
		$this->queue = new Channel($this->config->getMaxResources() * 16);
	}

	public function __destruct()
	{
		$this->queue->close();
	}

	/**
	 * 获取资源
	 * @return IPoolResource
	 */
	public function getResource(): IPoolResource
	{
		$result = $this->queue->pop();
		return $result ? $result : null;
	}

	/**
	 * 尝试获取资源，获取到则返回资源，没有获取到返回false
	 * @return IPoolResource|boolean
	 */
	public function tryGetResource()
	{
		return $this->queue->pop();
	}

	/**
	 * 建立队列
	 * @return void
	 */
	protected function buildQueue()
	{
		// 清空队列
		while($this->queue->pop())
		{

		}
		// 重新建立队列
		foreach($this->pool as $item)
		{
			$this->queue->push($item->getResource());
		}
	}
	
	/**
	 * 把资源加入队列
	 * @param IPoolResource $resource
	 * @return void
	 */
	protected function push(IPoolResource $resource)
	{
		$this->queue->push($resource);
	}
}