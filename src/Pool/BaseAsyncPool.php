<?php

namespace Imi\Pool;

use Imi\Pool\Interfaces\IPoolResource;
use Imi\Util\Coroutine;
use Swoole\Coroutine\Channel;

/**
 * 异步池子，必须用在协程中
 * 支持协程挂起等待连接被释放.
 */
abstract class BaseAsyncPool extends BasePool
{
    /**
     * 队列.
     *
     * @var \Swoole\Coroutine\Channel
     */
    protected $queue;

    /**
     * 关闭池子，释放所有资源.
     *
     * @return void
     */
    public function close()
    {
        parent::close();
        $this->queue->close();
    }

    /**
     * 初始化队列.
     *
     * @return void
     */
    protected function initQueue()
    {
        $this->queue = new Channel($this->config->getMaxResources());
    }

    /**
     * 获取资源.
     *
     * @return IPoolResource
     */
    public function getResource()
    {
        $selectResult = true;
        $queue = $this->queue;
        $config = $this->config;
        $waitTimeoutFloat = $config->getWaitTimeout() / 1000;
        if ($this->getFree() <= 0)
        {
            if ($this->getCount() < $config->getMaxResources())
            {
                // 没有空闲连接，当前连接数少于最大连接数
                $this->addResource();
            }
            else
            {
                $selectResult = $queue->pop($waitTimeoutFloat);
                if (false === $selectResult)
                {
                    throw new \RuntimeException(sprintf('AsyncPool [%s] getResource timeout', $this->getName()));
                }
            }
        }
        if (true === $selectResult)
        {
            $poolItem = $queue->pop();
        }
        else
        {
            $poolItem = $selectResult;
        }
        /** @var \Imi\Pool\PoolItem $poolItem */
        // @phpstan-ignore-next-line
        if (!$poolItem)
        {
            throw new \RuntimeException(sprintf('AsyncPool [%s] getResource failed', $this->getName()));
        }
        if (!$poolItem->lock($waitTimeoutFloat))
        {
            throw new \RuntimeException(sprintf('AsyncPool [%s] lock resource failed', $this->getName()));
        }
        try
        {
            $resource = $poolItem->getResource();
            if (($config->isCheckStateWhenGetResource() && !$resource->checkState()))
            {
                $resource->close();
                if (!$resource->open())
                {
                    throw new \RuntimeException(sprintf('AsyncPool [%s] getResource failed', $this->getName()));
                }
            }
        }
        catch (\Throwable $th)
        {
            $poolItem->release();
            throw $th;
        }

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
        $queue = $this->queue;
        // Coroutine\Channel::select()/->pop() 最小超时时间1毫秒
        $result = $queue->pop(0.001);
        if (false === $result)
        {
            return false;
        }
        if (true === $result)
        {
            $poolItem = $queue->pop();
        }
        else
        {
            $poolItem = $result;
        }
        /** @var \Imi\Pool\PoolItem $poolItem */
        // @phpstan-ignore-next-line
        if (!$poolItem)
        {
            throw new \RuntimeException(sprintf('AsyncPool [%s] getResource failed', $this->getName()));
        }
        if (!$poolItem->lock())
        {
            throw new \RuntimeException(sprintf('AsyncPool [%s] lock resource failed', $this->getName()));
        }
        try
        {
            $resource = $poolItem->getResource();
            if ($this->config->isCheckStateWhenGetResource() && !$resource->checkState())
            {
                $resource->close();
                if (!$resource->open())
                {
                    throw new \RuntimeException(sprintf('AsyncPool [%s] tryGetResource failed', $this->getName()));
                }
            }
        }
        catch (\Throwable $th)
        {
            $poolItem->release();
            throw $th;
        }

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
        $queue = $this->queue;
        while (!$queue->isEmpty())
        {
            $queue->pop();
        }
        if ($this->pool)
        {
            // 重新建立队列
            foreach ($this->pool as $item)
            {
                if ($item->isFree())
                {
                    $queue->push($item);
                }
            }
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
            if (Coroutine::isIn())
            {
                $this->queue->push($poolItem);
            }
            else
            {
                go(function () use ($poolItem) {
                    $this->queue->push($poolItem);
                });
            }
        }
    }

    /**
     * 获取当前池子中空闲资源总数.
     *
     * @return int
     */
    public function getFree()
    {
        return $this->queue->length();
    }
}
