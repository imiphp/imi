<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Tests\Manager;

use Imi\ConnectionCenter\Handler\Pool\PoolConfig;
use Imi\ConnectionCenter\Handler\Pool\PoolConnectionManagerConfig;
use PHPUnit\Framework\TestCase;

class PoolConnectionManagerConfigTest extends TestCase
{
    public function test1(): void
    {
        $config = new PoolConnectionManagerConfig('test', true, null, [
            'pool' => [
                'minResources'                 => 1,
                'maxResources'                 => 2,
                'gcInterval'                   => 3,
                'maxActiveTime'                => 4,
                'waitTimeout'                  => 5,
                'maxUsedTime'                  => 6,
                'maxIdleTime'                  => 7,
                'heartbeatInterval'            => 8,
            ],
        ]);
        $this->assertEquals('test', $config->getDriver());
        $this->assertTrue($config->isEnableStatistics());
        $this->assertEquals(1, $config->getPool()->getMinResources());
        $this->assertEquals(2, $config->getPool()->getMaxResources());
        $this->assertEquals(3, $config->getPool()->getGcInterval());
        $this->assertEquals(4, $config->getPool()->getMaxActiveTime());
        $this->assertEquals(5, $config->getPool()->getWaitTimeout());
        $this->assertEquals(6, $config->getPool()->getMaxUsedTime());
        $this->assertEquals(7, $config->getPool()->getMaxIdleTime());
        $this->assertEquals(8, $config->getPool()->getHeartbeatInterval());
    }

    public function test2(): void
    {
        $pool = new PoolConfig();
        $config = new PoolConnectionManagerConfig('test', true, $pool, [
            'pool' => [
                'minResources'                 => 1,
                'maxResources'                 => 2,
                'gcInterval'                   => 3,
                'maxActiveTime'                => 4,
                'waitTimeout'                  => 5,
                'maxUsedTime'                  => 6,
                'maxIdleTime'                  => 7,
                'heartbeatInterval'            => 8,
            ],
        ]);
        $this->assertEquals('test', $config->getDriver());
        $this->assertTrue($config->isEnableStatistics());
        $this->assertEquals(1, $config->getPool()->getMinResources());
        $this->assertEquals(32, $config->getPool()->getMaxResources());
        $this->assertEquals(60, $config->getPool()->getGcInterval());
        $this->assertNull($config->getPool()->getMaxActiveTime());
        $this->assertEquals(3, $config->getPool()->getWaitTimeout());
        $this->assertNull($config->getPool()->getMaxUsedTime());
        $this->assertNull($config->getPool()->getMaxIdleTime());
        $this->assertEquals(60, $config->getPool()->getHeartbeatInterval());
    }

    public function testNoDriver(): void
    {
        $this->expectExceptionMessage('ConnectionManager config [driver] not found');
        new PoolConnectionManagerConfig();
    }
}
