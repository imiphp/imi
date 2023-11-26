<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Tests\LoadBalancer;

class WeightLoadBalancerTest extends BaseLoadBalancerTestCase
{
    protected string $class = \Imi\ConnectionCenter\LoadBalancer\WeightLoadBalancer::class;
}
