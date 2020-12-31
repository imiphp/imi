<?php

declare(strict_types=1);

namespace Imi\Pool;

use Imi\Pool\Interfaces\IPoolResource;

/**
 * 池子中存储的对象
 */
class PoolItem
{
    /**
     * 资源对象
     *
     * @var \Imi\Pool\Interfaces\IPoolResource
     */
    protected IPoolResource $resource;

    /**
     * 被使用的次数.
     *
     * @var int
     */
    protected int $usageCount = 0;

    /**
     * 是否空闲状态
     *
     * @var bool
     */
    protected bool $isFree = true;

    /**
     * 创建时间的时间戳.
     *
     * @var float
     */
    protected float $createTime = 0;

    /**
     * 最后一次使用的时间戳.
     *
     * @var float
     */
    protected float $lastUseTime = 0;

    /**
     * 最后一次被释放的时间戳.
     *
     * @var float
     */
    protected float $lastReleaseTime = 0;

    public function __construct(\Imi\Pool\Interfaces\IPoolResource $resource)
    {
        $this->resource = $resource;
        $this->createTime = microtime(true);
    }

    /**
     * Get 资源对象
     *
     * @return \Imi\Pool\Interfaces\IPoolResource
     */
    public function getResource(): IPoolResource
    {
        return $this->resource;
    }

    /**
     * Get 被使用的次数.
     *
     * @return int
     */
    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    /**
     * 是否空闲状态
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return $this->isFree;
    }

    /**
     * 锁定.
     *
     * @return bool
     */
    public function lock(): bool
    {
        if ($this->isFree)
        {
            ++$this->usageCount;
            $this->isFree = false;
            $this->lastUseTime = microtime(true);

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 释放.
     *
     * @return void
     */
    public function release()
    {
        $this->isFree = true;
        $this->lastReleaseTime = microtime(true);
    }

    /**
     * Get 创建时间的时间戳.
     *
     * @return float
     */
    public function getCreateTime(): float
    {
        return $this->createTime;
    }

    /**
     * Get 最后一次使用的时间戳.
     *
     * @return float
     */
    public function getLastUseTime(): float
    {
        return $this->lastUseTime;
    }

    /**
     * Get 最后一次被释放的时间戳.
     *
     * @return float
     */
    public function getLastReleaseTime(): float
    {
        return $this->lastReleaseTime;
    }
}
