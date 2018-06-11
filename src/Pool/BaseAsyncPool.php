<?php
namespace Imi\Pool;

use Swoole\Coroutine\Channel;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Util\Coroutine;

/**
 * 异步池子，必须用在协程中
 * 支持协程挂起等待连接被释放
 */
abstract class BaseAsyncPool extends BasePool
{
	/**
	 * 队列
	 * @var \Swoole\Coroutine\Channel
	 */
	protected $queue;

	public function __destruct()
	{
		$this->queue->close();
	}

	/**
	 * 初始化队列
	 * @return void
	 */
	protected function initQueue()
	{
		$this->queue = new Channel($this->config->getMaxResources());
	}

	/**
	 * 获取资源
	 * @return IPoolResource
	 */
	public function getResource(): IPoolResource
	{
		if($this->getFree() <= 0)
		{
			if($this->getCount() < $this->config->getMaxResources())
			{
				// 没有空闲连接，当前连接数少于最大连接数
				$this->addResource();
			}
			else 
			{
				// 等待其他协程使用完成后释放连接
				$read = [$this->queue];
				$write = null;
				$selectResult = Channel::select($read, $write, $this->config->getWaitTimeout() / 1000);
				if(false === $selectResult)
				{
					throw new \RuntimeException('AsyncPool getResource timeout');
				}
			}
		}
		return $this->queue->pop();
	}

	/**
	 * 尝试获取资源，获取到则返回资源，没有获取到返回false
	 * @return IPoolResource|boolean
	 */
	public function tryGetResource()
	{
		$read = [$this->queue];
		// Coroutine\Channel::select() 最小超时时间1毫秒
		$result = Channel::select($read, null, 0.001);
		if(false === $result)
		{
			return false;
		}
		return $this->queue->pop();
	}

	/**
	 * 建立队列
	 * @return void
	 */
	protected function buildQueue()
	{
		// 清空队列
		$count = $this->getFree();
		for($i = 0; $i < $count; ++$i)
		{
			$this->queue->pop();
		}
		// 重新建立队列
		foreach($this->pool as $item)
		{
			$this->queue->push($item->getResource());
		}
		$this->free = $this->queue->length();
	}
	
	/**
	 * 把资源加入队列
	 * @param IPoolResource $resource
	 * @return void
	 */
	protected function push(IPoolResource $resource)
	{
		if(Coroutine::isIn())
		{
			$this->queue->push($resource);
		}
		else
		{
			go(function() use($resource){
				$this->queue->push($resource);
			});
		}
	}

	/**
	 * 获取当前池子中空闲资源总数
	 * @return int
	 */
	public function getFree()
	{
		return $this->queue->length();
	}
}