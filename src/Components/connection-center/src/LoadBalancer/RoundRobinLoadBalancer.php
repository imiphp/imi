<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\LoadBalancer;

use Imi\ConnectionCenter\Contract\AbstractConnectionLoadBalancer;
use Imi\ConnectionCenter\Contract\IConnectionConfig;

/**
 * 轮询-负载均衡
 */
class RoundRobinLoadBalancer extends AbstractConnectionLoadBalancer
{
    private int $position = 0;

    /**
     * @param IConnectionConfig[] $configs
     */
    public function setConfigs(array $configs): self
    {
        parent::setConfigs($configs);
        if ($configs)
        {
            $this->position = random_int(0, \count($configs) - 1);
        }
        else
        {
            $this->position = 0;
        }

        return $this;
    }

    public function choose(): ?IConnectionConfig
    {
        $maxIndex = \count($configs = $this->getConfigs()) - 1;
        $position = &$this->position;
        if (++$position > $maxIndex)
        {
            $position = 0;
        }

        return $configs[$position] ?? null;
    }
}
