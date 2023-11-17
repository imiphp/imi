<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

abstract class AbstractConnectionManagerConfig implements IConnectionManagerConfig
{
    public function __construct(protected ?string $driver = null, protected ?bool $enableStatistics = null, protected array $config = [])
    {
        foreach ($config as $key => $value)
        {
            $this->{$key} = $value;
        }
        if (null === $this->driver)
        {
            throw new \InvalidArgumentException('ConnectionManager config [driver] not found');
        }
        $this->enableStatistics ??= false;
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
}
