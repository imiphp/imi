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
     */
    protected ?IPoolResource $resource = null;

    /**
     * 被使用的次数.
     */
    protected int $usageCount = 0;

    /**
     * 是否空闲状态
     */
    protected bool $isFree = true;

    /**
     * 创建时间的时间戳.
     */
    protected float $createTime = 0;

    /**
     * 最后一次使用的时间戳.
     */
    protected float $lastUseTime = 0;

    /**
     * 最后一次被释放的时间戳.
     */
    protected float $lastReleaseTime = 0;

    public function __construct(IPoolResource $resource)
    {
        $this->resource = $resource;
        $this->createTime = microtime(true);
    }

    /**
     * Get 资源对象
     */
    public function getResource(): IPoolResource
    {
        return $this->resource;
    }

    /**
     * Get 被使用的次数.
     */
    public function getUsageCount(): int
    {
        return $this->usageCount;
    }

    /**
     * 是否空闲状态
     */
    public function isFree(): bool
    {
        return $this->isFree;
    }

    /**
     * 锁定.
     */
    public function lock(float $timeout = 0): bool
    {
        if ($this->isFree)
        {
            ++$this->usageCount;
            $this->isFree = false;
            $this->lastUseTime = microtime(true);

            return true;
        }

        return false;
    }

    /**
     * 释放.
     */
    public function release(): void
    {
        $this->isFree = true;
        $this->lastReleaseTime = microtime(true);
    }

    /**
     * Get 创建时间的时间戳.
     */
    public function getCreateTime(): float
    {
        return $this->createTime;
    }

    /**
     * Get 最后一次使用的时间戳.
     */
    public function getLastUseTime(): float
    {
        return $this->lastUseTime;
    }

    /**
     * Get 最后一次被释放的时间戳.
     */
    public function getLastReleaseTime(): float
    {
        return $this->lastReleaseTime;
    }
}
