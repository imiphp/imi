<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\LoadBalancer;

use Imi\ConnectionCenter\Contract\AbstractConnectionLoadBalancer;
use Imi\ConnectionCenter\Contract\IConnectionConfig;
use Imi\Util\Random;

/**
 * 权重-负载均衡
 */
class WeightLoadBalancer extends AbstractConnectionLoadBalancer
{
    public function choose(): ?IConnectionConfig
    {
        $configs = $this->getConfigs();
        $weightSum = 0;
        foreach ($configs as $config)
        {
            $weight = $config->getWeight();
            if ($weight > 0)
            {
                $weightSum += $weight;
            }
        }
        if ($weightSum <= 0)
        {
            return null;
        }
        $randomValue = Random::number(1, $weightSum);
        foreach ($configs as $config)
        {
            $randomValue -= $config->getWeight();
            if ($randomValue <= 0)
            {
                return $config;
            }
        }

        return $config ?? null; // @codeCoverageIgnore
    }
}
