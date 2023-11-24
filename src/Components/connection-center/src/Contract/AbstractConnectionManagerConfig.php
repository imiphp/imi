<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

abstract class AbstractConnectionManagerConfig implements IConnectionManagerConfig
{
    public function __construct(protected ?string $driver = null, protected ?bool $enableStatistics = null,
        /**
         * 当前请求上下文资源检查状态间隔，单位：支持小数的秒.
         *
         * 为 null/0 则每次都检查
         */
        protected ?float $requestResourceCheckInterval = null,
        /**
         * 是否在获取资源时检查状态
         */
        protected ?bool $checkStateWhenGetResource = null,
        protected array $config = []
    ) {
        foreach ($config as $key => $value)
        {
            if (property_exists($this, $key) && !isset($this->{$key}))
            {
                $this->{$key} = $value;
            }
        }
        if (null === $this->driver)
        {
            throw new \InvalidArgumentException('ConnectionManager config [driver] not found');
        }
        $this->enableStatistics ??= false;
        $this->requestResourceCheckInterval ??= 30;
        $this->checkStateWhenGetResource ??= false;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function isEnableStatistics(): bool
    {
        return $this->enableStatistics;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getRequestResourceCheckInterval(): ?float
    {
        return $this->requestResourceCheckInterval;
    }

    public function isCheckStateWhenGetResource(): bool
    {
        return $this->checkStateWhenGetResource;
    }
}
