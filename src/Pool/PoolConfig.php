<?php
namespace Imi\Pool;

use Imi\Bean\Annotation\Bean;
use Imi\Pool\Interfaces\IPoolConfig;

/**
 * 池子配置
 * @Bean("PoolConfig")
 */
class PoolConfig implements IPoolConfig
{
    /**
     * 池子中最多资源数
     * @var integer
     */
    protected $maxResources = 10;

    /**
     * 池子中最少资源数
     * @var integer
     */
    protected $minResources = 1;

    /**
     * 资源回收时间间隔，单位：秒
     * @var integer
     */
    protected $gcInterval = 60;

    /**
     * 资源最大存活时间，单位：秒
     * 
     * 为 null 则不限制
     * 
     * @var integer|null
     */
    protected $maxActiveTime;

    /**
     * 等待资源最大超时时间，单位：毫秒
     * @var integer
     */
    protected $waitTimeout = 3000;

    /**
     * 每次获取资源最长使用时间，单位：秒
     * 
     * 为 null 则不限制
     * 
     * @var float|null
     */
    protected $maxUsedTime;

    /**
     * 当前请求上下文资源检查状态间隔，单位：支持小数的秒
     *
     * @var double
     */
    protected $requestResourceCheckInterval = 3;

    /**
     * 资源配置模式
     *
     * @var int
     */
    protected $resourceConfigMode = ResourceConfigMode::TURN;

    public function __construct($option = [])
    {
        foreach($option as $k => $v)
        {
            $this->$k = $v;
        }
    }

    /**
     * 池子中最多资源数
     * @return int
     */
    public function getMaxResources()
    {
        return $this->maxResources;
    }

    /**
     * 池子中最少资源数
     * @return int
     */
    public function getMinResources()
    {
        return $this->minResources;
    }

    /**
     * 获取资源回收时间间隔，单位：秒
     * @return int|null
     */
    public function getGCInterval()
    {
        return $this->gcInterval;
    }

    /**
     * 获取资源最大存活时间，单位：秒
     * @return int
     */
    public function getMaxActiveTime()
    {
        return $this->maxActiveTime;
    }

    /**
     * 获取等待资源最大超时时间，单位：毫秒
     * @return int
     */
    public function getWaitTimeout()
    {
        return $this->waitTimeout;
    }

    /**
     * Set 池子中最多资源数
     *
     * @param integer $maxResources 池子中最多资源数
     *
     * @return static
     */ 
    public function setMaxResources($maxResources)
    {
        $this->maxResources = $maxResources;

        return $this;
    }

    /**
     * Set 池子中最少资源数
     *
     * @param integer $minResources 池子中最少资源数
     *
     * @return static
     */ 
    public function setMinResources($minResources)
    {
        $this->minResources = $minResources;

        return $this;
    }

    /**
     * Set 资源回收时间间隔，单位：秒
     *
     * @param integer|null $gcInterval 获取资源回收时间间隔，单位：秒
     *
     * @return static
     */ 
    public function setGcInterval($gcInterval)
    {
        $this->gcInterval = $gcInterval;

        return $this;
    }

    /**
     * Set 资源最大存活时间，单位：秒
     *
     * @param integer $maxActiveTime 获取资源最大存活时间，单位：秒
     *
     * @return static
     */ 
    public function setMaxActiveTime($maxActiveTime)
    {
        $this->maxActiveTime = $maxActiveTime;

        return $this;
    }

    /**
     * Set 等待资源最大超时时间
     * @param integer $waitTimeout 等待资源最大超时时间
     *
     * @return static
     */ 
    public function setWaitTimeout($waitTimeout)
    {
        $this->waitTimeout = $waitTimeout;

        return $this;
    }

    /**
     * 获取资源配置模式
     *
     * @return int
     */
    public function getResourceConfigMode()
    {
        return $this->resourceConfigMode;
    }

    /**
     * 设置资源配置模式
     *
     * @param int $resourceConfigMode
     * @return static
     */
    public function setResourceConfigMode($resourceConfigMode)
    {
        $this->resourceConfigMode = $resourceConfigMode;

        return $this;
    }

    /**
     * Get 每次获取资源最长使用时间
     *
     * @return float|null
     */ 
    public function getMaxUsedTime()
    {
        return $this->maxUsedTime;
    }

    /**
     * Set 每次获取资源最长使用时间
     *
     * @param float|null $maxUsedTime 为 null 则不限制
     *
     * @return static
     */ 
    public function setMaxUsedTime($maxUsedTime)
    {
        $this->maxUsedTime = $maxUsedTime;

        return $this;
    }

    /**
     * 获取当前请求上下文资源检查状态间隔，单位：支持小数的秒
     *
     * @return double
     */
    public function getRequestResourceCheckInterval()
    {
        return $this->requestResourceCheckInterval;
    }

    /**
     * 设置当前请求上下文资源检查状态间隔，单位：支持小数的秒
     *
     * @param double $value
     * @return static
     */
    public function setRequestResourceCheckInterval($value)
    {
        $this->requestResourceCheckInterval = $value;
        return $this;
    }

}