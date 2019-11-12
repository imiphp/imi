<?php
namespace Imi\Pool\Interfaces;

/**
 * 池子配置接口
 */
interface IPoolConfig
{
    /**
     * 池子中最多资源数
     * @return int
     */
    public function getMaxResources();

    /**
     * 池子中最少资源数
     * @return int
     */
    public function getMinResources();

    /**
     * 获取资源回收时间间隔，单位：秒
     * @return int|null
     */
    public function getGCInterval();

    /**
     * 获取资源最大存活时间，单位：秒
     * @return int
     */
    public function getMaxActiveTime();

    /**
     * 获取等待资源最大超时时间，单位：毫秒
     * @return int
     */
    public function getWaitTimeout();

    /**
     * 获取资源配置模式
     *
     * @return int
     */
    public function getResourceConfigMode();
    
    /**
     * Set 池子中最多资源数
     *
     * @param integer $maxResources 池子中最多资源数
     *
     * @return static
     */ 
    public function setMaxResources($maxResources);

    /**
     * Set 池子中最少资源数
     *
     * @param integer $minResources 池子中最少资源数
     *
     * @return static
     */ 
    public function setMinResources($minResources);

    /**
     * Set 资源回收时间间隔，单位：秒
     *
     * @param integer|null $gcInterval 获取资源回收时间间隔，单位：秒
     *
     * @return static
     */ 
    public function setGcInterval($gcInterval);

    /**
     * Set 资源最大存活时间，单位：秒
     *
     * @param integer $maxActiveTime 获取资源最大存活时间，单位：秒
     *
     * @return static
     */ 
    public function setMaxActiveTime($maxActiveTime);

    /**
     * Set 等待资源最大超时时间
     * @param integer $waitTimeout 等待资源最大超时时间
     *
     * @return static
     */ 
    public function setWaitTimeout($waitTimeout);

    /**
     * 设置资源配置模式
     *
     * @param int $resourceConfigMode
     * @return static
     */
    public function setResourceConfigMode($resourceConfigMode);

    /**
     * Get 每次获取资源最长使用时间
     *
     * @return float|null
     */ 
    public function getMaxUsedTime();

    /**
     * Set 每次获取资源最长使用时间
     *
     * @param float|null $maxUsedTime 为 null 则不限制
     *
     * @return static
     */ 
    public function setMaxUsedTime($maxUsedTime);

    /**
     * 获取当前请求上下文资源检查状态间隔，单位：支持小数的秒
     *
     * @return double
     */
    public function getRequestResourceCheckInterval();

    /**
     * 设置当前请求上下文资源检查状态间隔，单位：支持小数的秒
     *
     * @param double $value
     * @return static
     */
    public function setRequestResourceCheckInterval($value);

    /**
     * 获取心跳时间间隔，单位：秒
     *
     * @return double
     */
    public function getHeartbeatInterval();

    /**
     * Set 心跳时间间隔，单位：秒
     *
     * @param double|null $heartbeatInterval 心跳时间间隔，单位：秒
     *
     * @return static
     */ 
    public function setHeartbeatInterval($heartbeatInterval);

    /**
     * 当获取资源时，是否检查状态
     *
     * @return boolean
     */
    public function isCheckStateWhenGetResource();

    /**
     * 设置获取资源时，是否检查状态
     *
     * @param bool $checkStateWhenGetResource
     * @return static
     */
    public function setCheckStateWhenGetResource($checkStateWhenGetResource);

}