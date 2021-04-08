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
     * 获取池子名称.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取池子配置.
     *
     * @return \Imi\Pool\Interfaces\IPoolConfig
     */
    public function getConfig(): Interfaces\IPoolConfig
    {
        return $this->config;
    }

    /**
     * 打开池子.
     */
    public function open(): void
    {
        // 初始化队列
        $this->initQueue();
        // 填充最少资源数
        $this->fillMinResources();
    }

    /**
     * 关闭池子，释放所有资源.
     */
    public function close(): void
    {
        foreach ($this->pool as $item)
        {
            $item->getResource()->close();
        }
        $this->pool = [];
        $this->initQueue();
    }

    /**
     * 释放资源占用.
     */
    public function release(IPoolResource $resource): void
    {
        $hash = $resource->hashCode();
        $pool = &$this->pool;
        if (isset($pool[$hash]))
        {
            $pool[$hash]->release();
            $resource->reset();
            $this->push($resource);
        }
    }

    /**
     * 资源回收.
     */
    public function gc(): void
    {
        $hasGC = false;
        $config = $this->config;
        $maxActiveTime = $config->getMaxActiveTime();
        $maxUsedTime = $config->getMaxUsedTime();
        $pool = &$this->pool;
        foreach ($this->pool as $key => $item)
        {
            if (
                (null !== $maxActiveTime && $item->isFree() && time() - $item->getCreateTime() >= $maxActiveTime) // 最大存活时间
                || (null !== $maxUsedTime && $item->getLastReleaseTime() < $item->getLastUseTime() && time() - $item->getLastUseTime() >= $maxUsedTime) // 每次获取资源最长使用时间
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

    /**
     * 填充最少资源数量.
     */
    public function fillMinResources(): void
    {
        while ($this->config->getMinResources() - $this->getCount() > 0)
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
            $resource->open();

            $hash = $resource->hashCode();
            $this->pool[$hash] = new PoolItem($resource);

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
     * 获得资源配置.
     *
     * @return mixed
     */
    public function getResourceConfig()
    {
        return $this->resourceConfig;
    }

    /**
     * 获取当前池子中资源总数.
     */
    public function getCount(): int
    {
        return \count($this->pool) + $this->addingResources;
    }

    /**
     * 获取当前池子中正在使用的资源总数.
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
