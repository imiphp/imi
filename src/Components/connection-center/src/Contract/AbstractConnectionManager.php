<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

use Imi\App;

abstract class AbstractConnectionManager implements IConnectionManager
{
    protected bool $available = true;

    protected ?IConnectionDriver $driver = null;

    protected IConnectionManagerConfig $config;

    public function getConfig(): IConnectionManagerConfig
    {
        return $this->config;
    }

    public function close(): void
    {
        if (!$this->available)
        {
            throw new \RuntimeException('Connection manager is unavailable');
        }
        $this->__close();
        $this->available = false;
    }

    abstract protected function __close(): void;

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function getDriver(): IConnectionDriver
    {
        if ($this->driver)
        {
            return $this->driver;
        }

        $driver = $this->config->getDriver();

        return $this->driver = App::newInstance($driver, $driver::createConnectionConfig($this->config->getConfig()));
    }

    protected function createInstance(bool $connect = true): object
    {
        if (!$this->available)
        {
            throw new \RuntimeException('Connection manager is unavailable');
        }
        $driver = $this->getDriver();
        // 创建连接
        $instance = $driver->createInstance();
        if ($connect)
        {
            // 连接
            $driver->connect($instance);
        }

        return $instance;
    }
}
