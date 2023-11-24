<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\Pool;

use Imi\App;
use Imi\ConnectionCenter\Connection;
use Imi\ConnectionCenter\Contract\AbstractConnectionManager;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Contract\IConnectionManagerConfig;
use Imi\ConnectionCenter\Contract\IConnectionManagerStatistics;
use Imi\ConnectionCenter\Enum\ConnectionStatus;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\Swoole\Util\Coroutine;
use Imi\Timer\Timer;
use Swoole\Coroutine\Channel;

/**
 * 连接池连接管理器.
 *
 * @property PoolConnectionManagerConfig $config
 */
class PoolConnectionManager extends AbstractConnectionManager
{
    protected ?PoolConnectionManagerWritableStatistics $statistics = null;

    /**
     * 队列.
     *
     * @var Channel<InstanceResource>|mixed
     */
    protected $queue;

    /**
     * 资源列表.
     *
     * @var \SplObjectStorage<InstanceResource>
     */
    protected \SplObjectStorage $resources;

    /**
     * @var \WeakMap<object, InstanceResource>
     */
    protected \WeakMap $instanceMap;

    /**
     * 垃圾回收定时器ID.
     */
    protected ?int $gcTimerId = null;

    /**
     * 心跳定时器ID.
     */
    protected ?int $heartbeatTimerId = null;

    /**
     * 心跳正在运行.
     */
    protected bool $heartbeatRunning = false;

    /**
     * 正在添加中的资源数量.
     */
    protected int $addingResources = 0;

    /**
     * @param PoolConnectionManagerConfig $config
     */
    public function __construct(IConnectionManagerConfig $config)
    {
        if (!$config instanceof PoolConnectionManagerConfig)
        {
            throw new \InvalidArgumentException(sprintf('%s require %s, but %s given', static::class, PoolConnectionManagerConfig::class, $config::class));
        }
        $this->config = $config;
        if ($config->isEnableStatistics())
        {
            $this->statistics = App::newInstance(PoolConnectionManagerWritableStatistics::class);
        }
        $this->instanceMap = new \WeakMap();
        // 初始化队列
        $this->queue = new Channel($config->getPool()->getMaxResources());
        $this->resources = new \SplObjectStorage();
        // 填充最少资源数
        $this->fillMinResources();
        // 定时资源回收
        $this->startAutoGC();
        // 心跳
        $this->startHeartbeat();
    }

    public static function createConfig(array $config): IConnectionManagerConfig
    {
        return new PoolConnectionManagerConfig(config: $config);
    }

    /**
     * 创建新连接.
     *
     * @return Connection
     */
    public function createConnection(): IConnection
    {
        $instance = $this->createInstance();
        if ($this->config->isEnableStatistics())
        {
            $this->statistics->addCreateConnectionTimes();
        }

        return new Connection($this, $instance);
    }

    public function getConnection(): IConnection
    {
        if (!$this->available)
        {
            throw new \RuntimeException('Connection manager is unavailable');
        }
        $config = $this->config;
        if ($enableStatistics = $config->isEnableStatistics())
        {
            $beginTime = microtime(true);
        }
        $poolConfig = $config->getPool();
        $waitTimeout = $poolConfig->getWaitTimeout();
        if ($this->getFree() <= 0 && $this->getCount() < $poolConfig->getMaxResources())
        {
            // 没有空闲连接，当前连接数少于最大连接数
            $this->addResource();
        }
        /** @var InstanceResource|false $resource */
        $resource = $this->queue->pop($waitTimeout);
        if (!$resource)
        {
            if (\SWOOLE_CHANNEL_TIMEOUT === $this->queue->errCode)
            {
                throw new \RuntimeException('Pool getResource timeout');
            }
            else
            {
                throw new \RuntimeException('Pool getResource failed');
            }
        }
        if (!$resource->lock($waitTimeout))
        {
            throw new \RuntimeException('Pool lock resource failed');
        }
        $driver = $this->getDriver();
        if ($config->isCheckStateWhenGetResource() && !$driver->checkAvailable($instance = $resource->getInstance()))
        {
            try
            {
                $driver->close($instance);
                $instance = $driver->connect($instance);
                $resource->setInstance($instance);
                $this->instanceMap[$instance] = $resource;
            }
            catch (\Throwable $th)
            {
                $this->removeResource($resource);
                throw $th;
            }
        }

        $connection = new Connection($this, $resource->getInstance());
        $resource->setConnection($connection);

        if ($enableStatistics)
        {
            $this->statistics->setGetConnectionTime(microtime(true) - $beginTime);
            $this->statistics->addGetConnectionTimes();
        }

        return $connection;
    }

    public function releaseConnection(IConnection $connection): void
    {
        if ($connection->getManager() !== $this)
        {
            throw new \RuntimeException(sprintf('Connection manager %s cannot release connection, because the connection manager of this connection is %s', static::class, $connection->getManager()::class));
        }
        if (ConnectionStatus::WaitRelease !== $connection->getStatus())
        {
            throw new \RuntimeException('Connection is not in wait release status');
        }
        $instance = $connection->getInstance();
        if (isset($this->instanceMap[$instance]))
        {
            $this->getDriver()->reset($instance);
            $resource = $this->instanceMap[$instance];
            $resource->release();
            // 防止 __destruct() 期间释放连接
            if (Coroutine::isIn())
            {
                $this->queue->push($resource);
            }
        }
        else
        {
            // 已分离连接直接关闭
            $this->getDriver()->close($instance);
        }
        if ($this->config->isEnableStatistics())
        {
            $this->statistics->addReleaseConnectionTimes();
        }
    }

    public function detachConnection(IConnection $connection): void
    {
        if (!$this->available)
        {
            throw new \RuntimeException('Connection manager is unavailable');
        }
        if ($connection->getManager() !== $this)
        {
            throw new \RuntimeException(sprintf('Connection manager %s cannot release connection, because the connection manager of this connection is %s', static::class, $connection->getManager()::class));
        }
        $instance = $connection->getInstance();
        if (!isset($this->instanceMap[$instance]))
        {
            throw new \RuntimeException('Connection is not in this connection manager');
        }
        $resource = $this->instanceMap[$instance];
        $this->resources->detach($resource);
        unset($this->instanceMap[$instance]);
    }

    protected function __close(): void
    {
        $this->stopAutoGC();
        $this->stopHeartbeat();
        $driver = $this->getDriver();
        // 释放连接
        foreach ($this->instanceMap as $instance => $resource)
        {
            if (($connection = $resource->getConnection()?->get()) && ConnectionStatus::Available === $connection->getStatus())
            {
                $connection->release();
            }
            $driver->close($instance);
            $this->resources->detach($resource);
        }
        $this->queue->close();
    }

    /**
     * @return PoolConnectionManagerStatistics
     */
    public function getStatistics(): IConnectionManagerStatistics
    {
        if ($this->config->isEnableStatistics())
        {
            return $this->statistics->toStatus($this->resources->count(), $this->queue->length());
        }
        else
        {
            throw new \RuntimeException('Connection manager statistics is disabled');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function fillMinResources(): void
    {
        $minResources = $this->config->getPool()->getMinResources();
        while ($this->getCount() < $minResources)
        {
            $this->addResource();
        }
    }

    /**
     * 添加资源.
     */
    protected function addResource(): InstanceResource
    {
        $addingResources = &$this->addingResources;
        try
        {
            ++$addingResources;
            $instance = $this->createInstance();
            $resource = new InstanceResource($instance);
            $this->resources->attach($resource);
            $this->instanceMap[$instance] = $resource;
            $this->queue->push($resource);

            return $resource;
        }
        finally
        {
            --$addingResources;
        }
    }

    public function removeResource(InstanceResource $resource, bool $buildQueue = false): void
    {
        $this->resources->detach($resource);
        if ($buildQueue)
        {
            $this->buildQueue();
        }
    }

    protected function buildQueue(): void
    {
        // 清空队列
        $queue = $this->queue;
        while (!$queue->isEmpty())
        {
            $queue->pop();
        }
        // 重新建立队列
        foreach ($this->resources as $resource)
        {
            if ($resource->isFree())
            {
                $queue->push($resource);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function gc(): void
    {
        $hasGC = false;
        $poolConfig = $this->config->getPool();
        $maxActiveTime = $poolConfig->getMaxActiveTime();
        $maxUsedTime = $poolConfig->getMaxUsedTime();
        $maxIdleTime = $poolConfig->getMaxIdleTime();
        $needGcIdleResource = null !== $maxIdleTime && $this->getCount() > $poolConfig->getMinResources();
        $time = microtime(true);
        /** @var InstanceResource $resource */
        foreach ($this->resources as $resource)
        {
            if (
                (null !== $maxActiveTime && $resource->isFree() && $time - $resource->getCreateTime() >= $maxActiveTime) // 最大存活时间
                || ($needGcIdleResource && $resource->isFree() && $time - $resource->getLastReleaseTime() >= $maxIdleTime) // 最大空闲时间
                || (null !== $maxUsedTime && $resource->getLastReleaseTime() < $resource->getLastUseTime() && $time - $resource->getLastUseTime() >= $maxUsedTime) // 每次获取资源最长使用时间
            ) {
                $this->resources->detach($resource);
                if (($connection = $resource->getConnection()?->get()) && ConnectionStatus::Available === $connection->getStatus())
                {
                    $connection->release();
                }
                $driver ??= $this->getDriver();
                $driver->close($resource->getInstance());
                $hasGC = true;
            }
        }
        if ($hasGC)
        {
            $this->fillMinResources();
            $this->buildQueue();
        }
    }

    /**
     * 开始自动垃圾回收.
     */
    public function startAutoGC(): void
    {
        $gcInterval = $this->config->getPool()->getGCInterval();
        if ($gcInterval > 0)
        {
            $this->gcTimerId = Timer::tick((int) ($gcInterval * 1000), $this->gc(...));
            Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], $this->stopAutoGC(...), \Imi\Util\ImiPriority::IMI_MIN + 1);
        }
    }

    /**
     * 停止自动垃圾回收.
     */
    public function stopAutoGC(): void
    {
        if (null !== $this->gcTimerId)
        {
            Event::off(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], $this->stopAutoGC(...));
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
            $hasGC = false;
            foreach ($this->resources as $resource)
            {
                if ($resource->lock(0.001))
                {
                    $driver ??= $this->getDriver();
                    try
                    {
                        $available = $driver->checkAvailable($resource->getInstance());
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
                            $resource->release();
                        }
                        else
                        {
                            $this->removeResource($resource);
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
        if (null !== ($heartbeatInterval = $this->config->getPool()->getHeartbeatInterval()))
        {
            $this->heartbeatTimerId = Timer::tick((int) ($heartbeatInterval * 1000), $this->heartbeat(...));
            Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], $this->stopHeartbeat(...), \Imi\Util\ImiPriority::IMI_MIN + 1);
        }
    }

    /**
     * 停止心跳维持资源.
     */
    public function stopHeartbeat(): void
    {
        if (null !== $this->heartbeatTimerId)
        {
            Event::off(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], $this->stopHeartbeat(...));
            Timer::del($this->heartbeatTimerId);
            $this->heartbeatTimerId = null;
        }
    }

    public function getFree(): int
    {
        return $this->queue->length();
    }

    public function getCount(): int
    {
        return \count($this->resources) + $this->addingResources;
    }
}
