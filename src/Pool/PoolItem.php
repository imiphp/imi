<?php
namespace Imi\Pool;

/**
 * 池子中存储的对象
 */
class PoolItem
{
    /**
     * 资源对象
     * @var \Imi\Pool\Interfaces\IPoolResource
     */
    protected $resource;

    /**
     * 被使用的次数
     * @var int
     */
    protected $usageCount = 0;

    /**
     * 是否空闲状态
     * @var boolean
     */
    protected $isFree = true;

    /**
     * 创建时间的时间戳
     * @var integer
     */
    protected $createTime = 0;

    /**
     * 最后一次使用的时间戳
     *
     * @var integer
     */
    protected $lastUseTime = 0;

    /**
     * 最后一次被释放的时间戳
     *
     * @var integer
     */
    protected $lastReleaseTime = 0;

    public function __construct(\Imi\Pool\Interfaces\IPoolResource $resource)
    {
        $this->resource = $resource;
        $this->createTime = microtime(true);
    }

    /**
     * Get 资源对象
     * @return \Imi\Pool\Interfaces\IPoolResource
     */ 
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get 被使用的次数
     * @return int
     */ 
    public function getUsageCount()
    {
        return $this->usageCount;
    }

    /**
     * 是否空闲状态
     * @return boolean
     */ 
    public function isFree()
    {
        return $this->isFree;
    }

    /**
     * 锁定
     * @return void
     */
    public function lock()
    {
        ++$this->usageCount;
        $this->isFree = false;
        $this->lastUseTime = microtime(true);
    }

    /**
     * 释放
     * @return void
     */
    public function release()
    {
        $this->isFree = true;
        $this->lastReleaseTime = microtime(true);
    }

    /**
     * Get 创建时间的时间戳
     *
     * @return  integer
     */ 
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get 最后一次使用的时间戳
     *
     * @return  integer
     */ 
    public function getLastUseTime()
    {
        return $this->lastUseTime;
    }

    /**
     * Get 最后一次被释放的时间戳
     *
     * @return  integer
     */ 
    public function getLastReleaseTime()
    {
        return $this->lastReleaseTime;
    }

}