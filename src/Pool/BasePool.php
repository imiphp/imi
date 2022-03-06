<?php

declare(strict_types=1);

namespace Imi\Pool;

use Imi\App;
use Imi\Pool\Interfaces\IPool;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Util\ArrayUtil;

abstract class BasePool implements IPool
{
    /**
     * 池子名称.
     */
    protected string $name = '';

    /**
     * 池子存储.
     *
     * @var \Imi\Pool\PoolItem[]
     */
    protected array $pool = [];

    /**
     * 配置.
     *
     * @var \Imi\Pool\Interfaces\IPoolConfig|array
     */
    protected $config;

    /**
     * 资源配置.
     *
     * @var mixed
     */
    protected $resourceConfig;

    /**
     * 当前配置序号.
     */
    protected int $configIndex = -1;

    /**
     * 正在添加中的资源数量.
     */
    protected int $addingResources = 0;

    /**
     * PoolItem 类型.
     */
    protected string $poolItemClass = PoolItem::class;

    /**
     * @param \Imi\Pool\Interfaces\IPoolConfig $config
     * @param mixed                            $resourceConfig
     */
    public function __construct(string $name, Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        $this->name = $name;
        if (null !== $config)
        {
            $this->config = $config;
        }
        if (!\is_array($resourceConfig) || ArrayUtil::isAssoc($resourceConfig))
        {
            $this->resourceConfig = [$resourceConfig];
        }
        else
        {
            $this->resourceConfig = $resourceConfig;
        }
    }

    public function __init(): void
    {
        if (\is_array($this->config))
        {
            $this->config = App::getBean(PoolConfig::class, $this->config);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(): Interfaces\IPoolConfig
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function open(): void
    {
        // 初始化队列
        $this->initQueue();
        // 填充最少资源数
        $this->fillMinResources();
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        if ($this->pool)
        {
            foreach ($this->pool as $item)
            {
                $item->getResource()->close();
            }
            $this->pool = [];
        }
        $this->initQueue();
    }

    /**
     * {@inheritDoc}
     */
    public function release(IPoolResource $resource): void
    {
        $hash = $resource->hashCode();
        $pool = &$this->pool;
        if ($resource->isOpened())
        {
            if (isset($pool[$hash]))
            {
                $resource->reset();
                $pool[$hash]->release();
                $this->push($resource);
            }
        }
        elseif (isset($pool[$hash]))
        {
            unset($pool[$hash]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function removeResource(IPoolResource $resource, bool $buildQueue = false): void
    {
        $hash = $resource->hashCode();
        $pool = &$this->pool;
        if (isset($pool[$hash]))
        {
            unset($pool[$hash]);
        }
        if ($buildQueue)
        {
            $this->buildQueue();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function gc(): void
    {
        $pool = &$this->pool;
        if ($pool)
        {
            $hasGC = false;
            $config = $this->config;
            $maxActiveTime = $config->getMaxActiveTime();
            $maxUsedTime = $config->getMaxUsedTime();
            $maxIdleTime = $config->getMaxIdleTime();
            $time = microtime(true);

            $needGcIdleResource = null !== $maxIdleTime && $this->getCount() > $config->getMinResources();

            foreach ($pool as $key => $item)
            {
                if (
                    (null !== $maxActiveTime && $item->isFree() && $time - $item->getCreateTime() >= $maxActiveTime) // 最大存活时间
                    || ($needGcIdleResource && $item->isFree() && $time - $item->getLastReleaseTime() >= $maxIdleTime) // 最大空闲时间
                    || (null !== $maxUsedTime && $item->getLastReleaseTime() < $item->getLastUseTime() && $time - $item->getLastUseTime() >= $maxUsedTime) // 每次获取资源最长使用时间
                    ) {
                    $item->getResource()->close();
                    unset($pool[$key]);
                    $hasGC = true;
                }
            }
            if ($hasGC)
            {
                $this->fillMinResources();
                $this->buildQueue();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function fillMinResources(): void
    {
        while ($this->getCount() < $this->config->getMinResources())
        {
            $this->addResource();
        }
    }

    /**
     * 添加资源.
     */
    protected function addResource(): IPoolResource
    {
        $addingResources = &$this->addingResources;
        try
        {
            ++$addingResources;
            $resource = $this->createResource();
            if (!$resource->open())
            {
                throw new \RuntimeException(sprintf('Open pool [%s] resource failed', $this->name));
            }

            $hash = $resource->hashCode();
            $this->pool[$hash] = new $this->poolItemClass($resource);

            $this->push($resource);

            return $resource;
        }
        finally
        {
            --$addingResources;
        }
    }

    /**
     * 初始化队列.
     */
    abstract protected function initQueue(): void;

    /**
     * 建立队列.
     */
    abstract protected function buildQueue(): void;

    /**
     * 创建资源.
     */
    abstract protected function createResource(): IPoolResource;

    /**
     * 把资源加入队列.
     */
    abstract protected function push(IPoolResource $resource): void;

    /**
     * {@inheritDoc}
     */
    public function getResourceConfig()
    {
        return $this->resourceConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function getCount(): int
    {
        return \count($this->pool) + $this->addingResources;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsed(): int
    {
        return $this->getCount() - $this->getFree();
    }

    /**
     * 获取下一个资源配置.
     *
     * @return mixed
     */
    protected function getNextResourceConfig()
    {
        $resourceConfig = &$this->resourceConfig;
        if (!isset($resourceConfig[1]))
        {
            return $resourceConfig[0];
        }
        switch ($this->config->getResourceConfigMode())
        {
            case ResourceConfigMode::RANDOM:
                $index = mt_rand(0, \count($resourceConfig) - 1);
                break;
            default:
                $maxIndex = \count($resourceConfig) - 1;
                $configIndex = &$this->configIndex;
                if (++$configIndex > $maxIndex)
                {
                    $configIndex = 0;
                }
                $index = $configIndex;
                break;
        }

        return $resourceConfig[$index];
    }
}
