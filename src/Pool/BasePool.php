<?php
namespace Imi\Pool;

use Imi\App;
use Imi\Util\Coroutine;
use Imi\Bean\BeanFactory;
use Imi\Pool\Interfaces\IPool;
use Imi\Pool\Interfaces\IPoolResource;

abstract class BasePool implements IPool
{
	/**
	 * 池子名称
	 * @var string
	 */
	protected $name;

	/**
	 * 池子存储
	 * @var \Imi\Pool\PoolItem[]
	 */
	protected $pool = [];

	/**
	 * 配置
	 * @var \Imi\Pool\Interfaces\IPoolConfig
	 */
	protected $config;

	/**
	 * 资源配置
	 * @var mixed
	 */
	protected $resourceConfig;

	/**
	 * 时间间隔定时器ID
	 * @var int
	 */
	protected $timerID;

	public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
	{
		$this->name = $name;
		if(null !== $config)
		{
			$this->config = $config;
		}
		$this->resourceConfig = $resourceConfig;
	}

	public function __init()
	{
		if(is_array($this->config))
		{
			$this->config = BeanFactory::newInstance(PoolConfig::class, $this->config);
		}
	}

	/**
	 * 获取池子名称
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * 获取池子配置
	 * @return \Imi\Pool\Interfaces\IPoolConfig
	 */
	public function getConfig(): \Imi\Pool\Interfaces\IPoolConfig
	{
		return $this->config;
	}
	
	/**
	 * 打开池子
	 * @return void
	 */
	public function open()
	{
		// 初始化队列
		$this->initQueue();
		// 填充最少资源数
		$this->fillMinResources();
		// 定时资源回收
		$this->stopAutoGC();
		$this->startAutoGC();
	}

	/**
	 * 关闭池子，释放所有资源
	 * @return void
	 */
	public function close()
	{
		$this->stopAutoGC();
		foreach($this->pool as $item)
		{
			$item->getResource()->close();
		}
	}

	/**
	 * 释放资源占用
	 * @param \Imi\Pool\Interfaces\IPoolResource $resource
	 * @return void
	 */
	public function release(IPoolResource $resource)
	{
		$hash = spl_object_hash($resource);
		if(isset($this->pool[$hash]))
		{
			$this->pool[$hash]->release();
			$resource->reset();
			$this->push($resource);
		}
	}

	/**
	 * 资源回收
	 * @return void
	 */
	public function gc()
	{
		$hasGC = false;
		foreach($this->pool as $key => $item)
		{
			if($item->isFree() && time() - $item->getCreateTime() >= $this->config->getMaxActiveTime())
			{
				$item->getResource()->close();
				unset($this->pool[$key]);
				$hasGC = true;
			}
		}
		if($hasGC)
		{
			$this->fillMinResources();
			$this->buildQueue();
		}
	}

	/**
	 * 填充最少资源数量
	 * @return void
	 */
	public function fillMinResources()
	{
		$count = $this->config->getMinResources() - count($this->pool);
		for($i = 0; $i < $count; ++$i)
		{
			$this->addResource();
		}
	}

	/**
	 * 添加资源
	 * @return IPoolResource
	 */
	protected function addResource()
	{
		$resource = $this->createResource();
		$resource->open();
		
		$hash = spl_object_hash($resource);
		$this->pool[$hash] = new PoolItem($resource);

		$this->push($resource);

		return $resource;
	}

	/**
	 * 初始化队列
	 * @return void
	 */
	protected abstract function initQueue();

	/**
	 * 建立队列
	 * @return void
	 */
	protected abstract function buildQueue();

	/**
	 * 创建资源
	 * @return \Imi\Pool\Interfaces\IPoolResource
	 */
	protected abstract function createResource(): \Imi\Pool\Interfaces\IPoolResource;

	/**
	 * 把资源加入队列
	 * @param IPoolResource $resource
	 * @return void
	 */
	protected abstract function push(IPoolResource $resource);

	/**
	 * 开始自动垃圾回收
	 * @return void
	 */
	public function startAutoGC()
	{
		$this->timerID = \swoole_timer_tick($this->config->getGCInterval() * 1000, [$this, 'gc']);
	}

	/**
	 * 停止自动垃圾回收
	 * @return void
	 */
	public function stopAutoGC()
	{
		if(null !== $this->timerID)
		{
			\swoole_timer_clear($this->timerID);
		}
	}

	/**
	 * 获得资源配置
	 * @return mixed
	 */
	public function getResourceConfig()
	{
		return $this->resourceConfig;
	}

	/**
	 * 获取当前池子中资源总数
	 * @return int
	 */
	public function getCount()
	{
		return count($this->pool);
	}

	/**
	 * 获取当前池子中正在使用的资源总数
	 * @return int
	 */
	public function getUsed()
	{
		return $this->getCount() - $this->getFree();
	}
}