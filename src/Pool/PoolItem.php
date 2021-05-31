<?php

namespace Imi\Pool;

use Imi\Util\Co\ChannelContainer;

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
    protected $resource;

    /**
     * 被使用的次数.
     *
     * @var int
     */
    protected $usageCount = 0;

    /**
     * 是否空闲状态
     *
     * @var bool
     */
    protected $isFree = true;

    /**
     * 创建时间的时间戳.
     *
     * @var float
     */
    protected $createTime = 0;

    /**
     * 最后一次使用的时间戳.
     *
     * @var float
     */
    protected $lastUseTime = 0;

    /**
     * 最后一次被释放的时间戳.
     *
     * @var float
     */
    protected $lastReleaseTime = 0;

    /**
     * @param \Imi\Pool\Interfaces\IPoolResource $resource
     */
    public function __construct(Interfaces\IPoolResource $resource)
    {
        $this->resource = $resource;
        $this->createTime = microtime(true);
    }

    public function __destruct()
    {
        $id = spl_object_hash($this);
        if (ChannelContainer::hasChannel($id))
        {
            ChannelContainer::removeChannel($id);
        }
    }

    /**
     * Get 资源对象
     *
     * @return \Imi\Pool\Interfaces\IPoolResource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get 被使用的次数.
     *
     * @return int
     */
    public function getUsageCount()
    {
        return $this->usageCount;
    }

    /**
     * 是否空闲状态
     *
     * @return bool
     */
    public function isFree()
    {
        return $this->isFree;
    }

    /**
     * 锁定.
     *
     * @return bool
     */
    public function lock(float $timeout = 0)
    {
        if ($this->isFree || ChannelContainer::pop(spl_object_hash($this), $timeout))
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
     *
     * @return void
     */
    public function release()
    {
        $this->isFree = true;
        $this->lastReleaseTime = microtime(true);
        $id = spl_object_hash($this);
        if (ChannelContainer::hasChannel($id))
        {
            ChannelContainer::push($id, true);
        }
    }

    /**
     * Get 创建时间的时间戳.
     *
     * @return float
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get 最后一次使用的时间戳.
     *
     * @return float
     */
    public function getLastUseTime()
    {
        return $this->lastUseTime;
    }

    /**
     * Get 最后一次被释放的时间戳.
     *
     * @return float
     */
    public function getLastReleaseTime()
    {
        return $this->lastReleaseTime;
    }
}
