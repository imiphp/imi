<?php

declare(strict_types=1);

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
     */
    protected \SplQueue $queue;

    public function __destruct()
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function initQueue(): void
    {
        $this->queue = new \SplQueue();
    }

    /**
     * {@inheritDoc}
     */
    public function getResource(): IPoolResource
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
        $resource = $poolItem->getResource();
        if ($config->isCheckStateWhenGetResource() && !$resource->checkState())
        {
            $resource->close();
            if (!$resource->open())
            {
                throw new \RuntimeException(sprintf('SyncPool [%s] getResource failed', $this->getName()));
            }
        }
        $poolItem->lock();

        return $resource;
    }

    /**
     * {@inheritDoc}
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
        $resource = $poolItem->getResource();
        if ($this->config->isCheckStateWhenGetResource() && !$resource->checkState())
        {
            $resource->close();
            if (!$resource->open())
            {
                throw new \RuntimeException(sprintf('SyncPool [%s] tryGetResource failed', $this->getName()));
            }
        }
        $poolItem->lock();

        return $resource;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildQueue(): void
    {
        // 清空队列
        $this->initQueue();
        $queue = $this->queue;
        if ($this->pool)
        {
            // 重新建立队列
            foreach ($this->pool as $item)
            {
                $queue->push($item);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function push(IPoolResource $resource): void
    {
        $poolItem = $this->pool[$resource->hashCode()] ?? null;
        if ($poolItem)
        {
            $this->queue->push($poolItem);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getFree(): int
    {
        return $this->queue->count();
    }
}
