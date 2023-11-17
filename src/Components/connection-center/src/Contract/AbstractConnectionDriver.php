<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

abstract class AbstractConnectionDriver implements IConnectionDriver
{
    public function __construct(protected IConnectionConfig $config)
    {
    }

    /**
     * 设置连接配置.
     */
    public function setConnectionConfig(IConnectionConfig $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * 获取连接配置.
     */
    public function getConnectionConfig(): IConnectionConfig
    {
        return $this->config;
    }
}
