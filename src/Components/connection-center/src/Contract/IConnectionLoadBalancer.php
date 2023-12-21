<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

/**
 * 连接负载均衡器.
 */
interface IConnectionLoadBalancer
{
    /**
     * @param IConnectionConfig[] $configs
     */
    public function __construct(array $configs);

    /**
     * @param IConnectionConfig[] $configs
     */
    public function setConfigs(array $configs): self;

    /**
     * @return IConnectionConfig[]
     */
    public function getConfigs(): array;

    public function choose(): ?IConnectionConfig;
}
