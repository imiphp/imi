<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\LoadBalancer;

use Imi\ConnectionCenter\Contract\AbstractConnectionLoadBalancer;
use Imi\ConnectionCenter\Contract\IConnectionConfig;

/**
 * 随机-负载均衡
 */
class RandomLoadBalancer extends AbstractConnectionLoadBalancer
{
    public function choose(): ?IConnectionConfig
    {
        if (($count = \count($configs = $this->getConfigs())) > 0)
        {
            return $configs[random_int(0, $count - 1)] ?? null;
        }
        else
        {
            return null;
        }
    }
}
