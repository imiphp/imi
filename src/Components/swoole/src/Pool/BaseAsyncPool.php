<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool;

use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Pool\BasePool;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Swoole\Util\Coroutine;
use Imi\Timer\Timer;
use Imi\Worker;
use Swoole\Coroutine\Channel;

/**
 * 异步池子，必须用在协程中
 * 支持协程挂起等待连接被释放.
 */
abstract class BaseAsyncPool extends BasePool
{
    /**
     * 队列.
     */
    protected Channel $queue;

    /**
     * 垃圾回收定时器ID.
     */
    protected ?int $gcTimerId = null;

    /**
     * 心跳定时器ID.
     */
    protected ?int $heartbeatTimerId = null;

    /**
     * PoolItem 类型.
     */
    protected string $poolItemClass = PoolItem::class;

    protected bool $heartbeatRunning = false;

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->stopAutoGC();
        $this->stopHeartbeat();
        parent::close();
        $this->queue->close();
    }

    /**
     * {@inheritDoc}
     */
    public function open(): void
    {
        // 初始化队列
        $this->initQueue();
        if (Coroutine::isIn())
        {
            // 填充最少资源数
            $this->fillMinResources();
        }
        // 定时资源回收
        $this->stopAutoGC();
        $this->startAutoGC();
        // 心跳
        $this->stopHeartbeat();
        $this->startHeartbeat();
    }

    /**
     * {@inheritDoc}
     */
    protected function initQueue(): void
    {
        $this->queue = new Channel($this->config->getMaxResources());
    }

    /**
     * {@inheritDoc}
     */
    public function getResource(): IPoolResource
    {
        $queue = $this->queue;
        $config = $this->config;
        $waitTimeoutFloat = $config->getWaitTimeout() / 1000;
        if ($this->getFree() <= 0 && $this->getCount() < $config->getMaxResources())
        {
            // 没有空闲连接，当前连接数少于最大连接数
            $this->addResource();
        }
        $poolItem = $queue->pop($waitTimeoutFloat);
        if (!$poolItem)
        {
            if (\SWOOLE_CHANNEL_TIMEOUT === $queue->errCode)
            {
                throw new \RuntimeException(sprintf('AsyncPool [%s] getResource timeout', $this->getName()));
            }
            else
            {
                throw new \RuntimeException(sprintf('AsyncPool [%s] getResource failed', $this->getName()));
            }
        }
        if (!$poolItem->lock($waitTimeoutFloat))
        {
            throw new \RuntimeException(sprintf('AsyncPool [%s] lock resource failed', $this->getName()));
        }
        $resource = $poolItem->getResource();
        try
        {
            if (!$resource->isOpened() || ($config->isCheckStateWhenGetResource() && !$resource->checkState()))
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
            $this->removeResource($resource);
            throw $th;
        }

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
        /** @var \Imi\Pool\PoolItem|false $poolItem */
        if (!$poolItem)
        {
            throw new \RuntimeException(sprintf('AsyncPool [%s] getResource failed', $this->getName()));
        }
        if (!$poolItem->lock(0.001))
        {
            throw new \RuntimeException(sprintf('AsyncPool [%s] lock resource failed', $this->getName()));
        }
        $resource = $poolItem->getResource();
        try
        {
            if (!$resource->isOpened() || ($this->config->isCheckStateWhenGetResource() && !$resource->checkState()))
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
            $this->removeResource($resource);
            throw $th;
        }

        return $resource;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildQueue(): void
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
     * {@inheritDoc}
     */
    protected function push(IPoolResource $resource): void
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
                \Swoole\Coroutine\run(function () use ($poolItem): void {
                    $this->queue->push($poolItem);
                });
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getFree(): int
    {
        return $this->queue->length();
    }

    /**
     * 开始自动垃圾回收.
     */
    public function startAutoGC(): void
    {
        if (null !== Worker::getWorkerId() || Coroutine::stats()['coroutine_num'] > 0)
        {
            $gcInterval = $this->config->getGCInterval();
            if ($gcInterval > 0)
            {
                $this->gcTimerId = Timer::tick($gcInterval * 1000, $this->gc(...));
                Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], function (): void {
                    $this->stopAutoGC();
                }, \Imi\Util\ImiPriority::IMI_MIN + 1);
            }
        }
    }

    /**
     * 停止自动垃圾回收.
     */
    public function stopAutoGC(): void
    {
        if (null !== $this->gcTimerId)
        {
            Timer::del($this->gcTimerId);
        }
    }

    /**
     * 心跳.
     */
    public function heartbeat(): void
    {
        if ($this->heartbeatRunning)
        {
            return;
        }
        try
        {
            $this->heartbeatRunning = true;
            $pool = &$this->pool;
            if ($pool)
            {
                $hasGC = false;
                foreach ($pool as $key => $item)
                {
                    if ($item->lock(0.001))
                    {
                        $resource = null;
                        try
                        {
                            $resource = $item->getResource();
                            $available = $resource->checkState();
                        }
                        catch (\Throwable $th)
                        {
                            $available = false;
                            Log::error($th);
                        }
                        finally
                        {
                            if ($available)
                            {
                                $item->release();
                            }
                            elseif ($resource)
                            {
                                $resource->close();
                                unset($pool[$key]);
                                $hasGC = true;
                            }
                        }
                    }
                }
                if ($hasGC)
                {
                    $this->fillMinResources();
                    $this->buildQueue();
                }
            }
        }
        finally
        {
            $this->heartbeatRunning = false;
        }
    }

    /**
     * 开始心跳维持资源.
     */
    public function startHeartbeat(): void
    {
        if ((null !== Worker::getWorkerId() || Coroutine::stats()['coroutine_num'] > 0) && null !== ($heartbeatInterval = $this->config->getHeartbeatInterval()))
        {
            $this->heartbeatTimerId = Timer::tick((int) ($heartbeatInterval * 1000), $this->heartbeat(...));
            Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], function (): void {
                $this->stopHeartbeat();
            }, \Imi\Util\ImiPriority::IMI_MIN + 1);
        }
    }

    /**
     * 停止心跳维持资源.
     */
    public function stopHeartbeat(): void
    {
        if (null !== $this->heartbeatTimerId)
        {
            Timer::del($this->heartbeatTimerId);
        }
    }
}
