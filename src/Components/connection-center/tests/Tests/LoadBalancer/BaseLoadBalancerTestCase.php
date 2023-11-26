<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Tests\LoadBalancer;

use Imi\ConnectionCenter\Contract\IConnectionLoadBalancer;
use Imi\ConnectionCenter\Test\Driver\TestDriverConfig;
use PHPUnit\Framework\TestCase;

abstract class BaseLoadBalancerTestCase extends TestCase
{
    protected string $class;

    public function test(): void
    {
        $configs = [
            TestDriverConfig::create(['test' => true, 'weight' => 1]),
            TestDriverConfig::create(['test' => false, 'weight' => 2]),
        ];
        /** @var IConnectionLoadBalancer $loadBalancer */
        $loadBalancer = new $this->class($configs);

        $this->assertEquals($configs, $loadBalancer->getConfigs());

        $config = $loadBalancer->choose();
        $this->assertNotNull($config);

        /** @var IConnectionLoadBalancer $loadBalancer */
        $loadBalancer = new $this->class([]);

        $this->assertEquals([], $loadBalancer->getConfigs());

        $config = $loadBalancer->choose();
        $this->assertNull($config);

        /** @var IConnectionLoadBalancer $loadBalancer */
        $loadBalancer = new $this->class($configs);

        $this->assertEquals($configs, $loadBalancer->getConfigs());

        for ($i = 0; $i < 2; ++$i)
        {
            $config = $loadBalancer->choose();
            $this->assertNotNull($config);
        }
    }
}
