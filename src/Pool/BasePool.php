<?php

declare(strict_types=1);

namespace Imi\Pool;

use Imi\Bean\BeanFactory;
use Imi\Pool\Interfaces\IPool;
use Imi\Pool\Interfaces\IPoolConfig;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Util\ArrayUtil;

abstract class BasePool implements IPool
{
    /**
     * 池子名称.
     *
     * @var string
     */
    protected string $name;

    /**
     * 池子存储.
     *
     * @var \Imi\Pool\PoolItem[]
     */
    protected array $pool = [];

    /**
     * 配置.
     *
     * @var \Imi\Pool\Interfaces\IPoolConfig
     */
    protected IPoolConfig $config;

    /**
     * 资源配置.
     *
     * @var mixed
     */
    protected $resourceConfig;

    /**
     * 当前配置序号.
     *
     * @var int
     */
    protected int $configIndex = -1;

    /**
     * 正在添加中的资源数量.
     *
     * @var int
     */
    protected int $addingResources = 0;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
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

    public function __init()
    {
        if (\is_array($this->config))
        {
            $this->config = BeanFactory::newInstance(PoolConfig::class, $this->config);
        }
    }

    /**
     * 获取池子名称.
     *
     * @return string
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
    public function getConfig(): \Imi\Pool\Interfaces\IPoolConfig
    {
        return $this->config;
    }

    /**
     * 打开池子.
     *
     * @return void
     */
    public function open()
    {
        // 初始化队列
        $this->initQueue();
        // 填充最少资源数
        $this->fillMinResources();
    }

    /**
     * 关闭池子，释放所有资源.
     *
     * @return void
     */
    public function close()
    {
        foreach ($this->pool as $item)
        {
            $item->getResource()->close();
        }
        $this->pool = [];
        $this->buildQueue();
    }

    /**
     * 释放资源占用.
     *
     * @param \Imi\Pool\Interfaces\IPoolResource $resource
     *
     * @return void
     */
    public function release(IPoolResource $resource)
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
     *
     * @return void
     */
    public function gc()
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
     *
     * @return void
     */
    public function fillMinResources()
    {
        while ($this->config->getMinResources() - $this->getCount() > 0)
        {
            $this->addResource();
        }
    }

    /**
     * 添加资源.
     *
     * @return IPoolResource
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
     *
     * @return void
     */
    abstract protected function initQueue();

    /**
     * 建立队列.
     *
     * @return void
     */
    abstract protected function buildQueue();

    /**
     * 创建资源.
     *
     * @return \Imi\Pool\Interfaces\IPoolResource
     */
    abstract protected function createResource(): \Imi\Pool\Interfaces\IPoolResource;

    /**
     * 把资源加入队列.
     *
     * @param IPoolResource $resource
     *
     * @return void
     */
    abstract protected function push(IPoolResource $resource);

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
     *
     * @return int
     */
    public function getCount(): int
    {
        return \count($this->pool) + $this->addingResources;
    }

    /**
     * 获取当前池子中正在使用的资源总数.
     *
     * @return int
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
