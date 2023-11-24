<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\Pool;

use Imi\Pool\ResourceConfigMode;

/**
 * 连接池连接管理器配置.
 */
class PoolConfig
{
    public function __construct(
        /**
         * 池子中最多资源数.
         */
        protected int $maxResources = 32,
        /**
         * 池子中最少资源数.
         */
        protected int $minResources = 1,
        /**
         * 资源回收时间间隔，单位：秒.
         */
        protected ?float $gcInterval = 60,
        /**
         * 资源最大存活时间，单位：秒.
         *
         * 为 null 则不限制
         */
        protected ?float $maxActiveTime = null,
        /**
         * 等待资源最大超时时间，单位：秒.
         */
        protected float $waitTimeout = 3,
        /**
         * 每次获取资源最长使用时间，单位：秒.
         *
         * 为 null 则不限制
         */
        protected ?float $maxUsedTime = null,
        /**
         * 资源创建后最大空闲回收时间，单位：秒.
         */
        protected ?float $maxIdleTime = null,
        /**
         * 心跳时间间隔，单位：秒.
         */
        protected ?float $heartbeatInterval = 60,
        /**
         * 资源配置模式.
         */
        protected int $resourceConfigMode = ResourceConfigMode::ROUND_ROBIN,
    ) {
    }

    public function getMaxResources(): int
    {
        return $this->maxResources;
    }

    public function getMinResources(): int
    {
        return $this->minResources;
    }

    public function getGCInterval(): ?float
    {
        return $this->gcInterval;
    }

    public function getMaxActiveTime(): ?float
    {
        return $this->maxActiveTime;
    }

    public function getWaitTimeout(): float
    {
        return $this->waitTimeout;
    }

    public function getResourceConfigMode(): int
    {
        return $this->resourceConfigMode;
    }

    public function getMaxUsedTime(): ?float
    {
        return $this->maxUsedTime;
    }

    public function getMaxIdleTime(): ?float
    {
        return $this->maxIdleTime;
    }

    public function getHeartbeatInterval(): ?float
    {
        return $this->heartbeatInterval;
    }
}
