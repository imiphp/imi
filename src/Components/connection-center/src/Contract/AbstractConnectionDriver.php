<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

abstract class AbstractConnectionDriver implements IConnectionDriver
{
    public function __construct(protected IConnectionLoadBalancer $connectionLoadBalancer)
    {
    }

    public function getConnectionLoadBalancer(): IConnectionLoadBalancer
    {
        return $this->connectionLoadBalancer;
    }

    public function createInstance(): object
    {
        $config = $this->connectionLoadBalancer->choose();
        if (!$config)
        {
            throw new \RuntimeException(sprintf('No connection config available in %s', static::class));
        }

        return $this->createInstanceByConfig($config);
    }

    abstract protected function createInstanceByConfig(IConnectionConfig $config): object;
}
