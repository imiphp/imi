<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

abstract class AbstractConnectionLoadBalancer implements IConnectionLoadBalancer
{
    /**
     * @var IConnectionConfig[]
     */
    protected array $configs;

    /**
     * @param IConnectionConfig[] $configs
     */
    public function __construct(array $configs)
    {
        $this->setConfigs($configs);
    }

    /**
     * @param IConnectionConfig[] $configs
     */
    public function setConfigs(array $configs): self
    {
        $this->configs = $configs;

        return $this;
    }

    /**
     * @return IConnectionConfig[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }
}
