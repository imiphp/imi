<?php

declare(strict_types=1);

namespace Imi\Pool\Interfaces;

/**
 * 池子配置接口.
 */
interface IPoolConfig
{
    /**
     * 池子中最多资源数.
     *
     * @return int
     */
    public function getMaxResources(): int;

    /**
     * 池子中最少资源数.
     *
     * @return int
     */
    public function getMinResources(): int;

    /**
     * 获取资源回收时间间隔，单位：秒.
     *
     * @return int|null
     */
    public function getGCInterval(): ?int;

    /**
     * 获取资源最大存活时间，单位：秒.
     *
     * @return int|null
     */
    public function getMaxActiveTime(): ?int;

    /**
     * 获取等待资源最大超时时间，单位：毫秒.
     *
     * @return int
     */
    public function getWaitTimeout(): int;

    /**
     * 获取资源配置模式.
     *
     * @return int
     */
    public function getResourceConfigMode(): int;

    /**
     * Set 池子中最多资源数.
     *
     * @param int $maxResources 池子中最多资源数
     *
     * @return static
     */
    public function setMaxResources(int $maxResources): self;

    /**
     * Set 池子中最少资源数.
     *
     * @param int $minResources 池子中最少资源数
     *
     * @return static
     */
    public function setMinResources(int $minResources): self;

    /**
     * Set 资源回收时间间隔，单位：秒.
     *
     * @param int|null $gcInterval 获取资源回收时间间隔，单位：秒
     *
     * @return static
     */
    public function setGcInterval(?int $gcInterval): self;

    /**
     * Set 资源最大存活时间，单位：秒.
     *
     * @param int|null $maxActiveTime 获取资源最大存活时间，单位：秒
     *
     * @return static
     */
    public function setMaxActiveTime(?int $maxActiveTime): self;

    /**
     * Set 等待资源最大超时时间.
     *
     * @param int $waitTimeout 等待资源最大超时时间
     *
     * @return static
     */
    public function setWaitTimeout(int $waitTimeout): self;

    /**
     * 设置资源配置模式.
     *
     * @param int $resourceConfigMode
     *
     * @return static
     */
    public function setResourceConfigMode(int $resourceConfigMode): self;

    /**
     * Get 每次获取资源最长使用时间.
     *
     * @return float|null
     */
    public function getMaxUsedTime(): ?float;

    /**
     * Set 每次获取资源最长使用时间.
     *
     * @param float|null $maxUsedTime 为 null 则不限制
     *
     * @return static
     */
    public function setMaxUsedTime(?float $maxUsedTime): self;

    /**
     * 获取当前请求上下文资源检查状态间隔，单位：支持小数的秒.
     *
     * @return float
     */
    public function getRequestResourceCheckInterval(): float;

    /**
     * 设置当前请求上下文资源检查状态间隔，单位：支持小数的秒.
     *
     * @param float $value
     *
     * @return static
     */
    public function setRequestResourceCheckInterval(float $value): self;

    /**
     * 获取心跳时间间隔，单位：秒.
     *
     * @return float|null
     */
    public function getHeartbeatInterval(): ?float;

    /**
     * Set 心跳时间间隔，单位：秒.
     *
     * @param float|null $heartbeatInterval 心跳时间间隔，单位：秒
     *
     * @return static
     */
    public function setHeartbeatInterval(?float $heartbeatInterval): self;

    /**
     * 当获取资源时，是否检查状态
     *
     * @return bool
     */
    public function isCheckStateWhenGetResource(): bool;

    /**
     * 设置获取资源时，是否检查状态
     *
     * @param bool $checkStateWhenGetResource
     *
     * @return static
     */
    public function setCheckStateWhenGetResource(bool $checkStateWhenGetResource): self;
}
