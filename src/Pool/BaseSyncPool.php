<?php

namespace Imi\Pool;

use Imi\Pool\Interfaces\IPoolResource;

/**
 * 同步池子，支持使用在大部分情况
 * 仅支持尝试获取后立即返回结果，如果获取失败返回null.
 */
abstract class BaseSyncPool extends BasePool
{
    /**
     * 队列.
     *
     * @var \SplQueue
     */
    protected $queue;

    public function __destruct()
    {
    }

    /**
     * 初始化队列.
     *
     * @return void
     */
    protected function initQueue()
    {
        $this->queue = new \SplQueue();
    }

    /**
     * 获取资源.
     *
     * @return IPoolResource
     */
    public function getResource()
    {
        $config = $this->config;
        if ($this->getFree() <= 0)
        {
            if ($this->getCount() < $config->getMaxResources())
            {
                // 没有空闲连接，当前连接数少于最大连接数
                $this->addResource();
            }
            else
            {
                throw new \RuntimeException(sprintf('SyncPool [%s] Maximum number of resources', $this->getName()));
            }
        }
        /** @var \Imi\Pool\PoolItem $poolItem */
        $poolItem = $this->queue->pop();
        if (!$poolItem)
        {
            throw new \RuntimeException(sprintf('SyncPool [%s] getResource failed', $this->getName()));
        }
        $resource = $poolItem->getResource();
        if (!$resource || ($config->isCheckStateWhenGetResource() && !$resource->checkState() && !$resource->close() && !$resource->open()))
        {
            throw new \RuntimeException(sprintf('SyncPool [%s] getResource failed', $this->getName()));
        }
        $poolItem->lock();

        return $resource;
    }

    /**
     * 尝试获取资源，获取到则返回资源，没有获取到返回false.
     *
     * @return IPoolResource|bool
     */
    public function tryGetResource()
    {
        if ($this->getFree() <= 0)
        {
            if ($this->getCount() < $this->config->getMaxResources())
            {
                // 没有空闲连接，当前连接数少于最大连接数
                $this->addResource();
            }
            else
            {
                return false;
            }
        }
        /** @var \Imi\Pool\PoolItem $poolItem */
        $poolItem = $this->queue->pop();
        if (!$poolItem)
        {
            throw new \RuntimeException(sprintf('SyncPool [%s] getResource failed', $this->getName()));
        }
        $resource = $poolItem->getResource();
        if (!$resource || ($this->config->isCheckStateWhenGetResource() && !$resource->checkState() && !$resource->close() && !$resource->open()))
        {
            throw new \RuntimeException(sprintf('SyncPool [%s] tryGetResource failed', $this->getName()));
        }
        $poolItem->lock();

        return $resource;
    }

    /**
     * 建立队列.
     *
     * @return void
     */
    protected function buildQueue()
    {
        // 清空队列
        $this->initQueue();
        $queue = $this->queue;
        // 重新建立队列
        foreach ($this->pool as $item)
        {
            $queue->push($item);
        }
    }

    /**
     * 把资源加入队列.
     *
     * @param IPoolResource $resource
     *
     * @return void
     */
    protected function push(IPoolResource $resource)
    {
        $poolItem = $this->pool[$resource->hashCode()] ?? null;
        if ($poolItem)
        {
            $this->queue->push($poolItem);
        }
    }

    /**
     * 获取当前池子中空闲资源总数.
     *
     * @return int
     */
    public function getFree()
    {
        return $this->queue->count();
    }
}
