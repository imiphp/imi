<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Tests;

use Imi\ConnectionCenter\Contract\ConnectionManagerConfig;
use Imi\ConnectionCenter\Contract\IConnectionDriver;
use Imi\ConnectionCenter\Contract\IConnectionLoadBalancer;
use Imi\ConnectionCenter\LoadBalancer\RandomLoadBalancer;
use Imi\ConnectionCenter\Test\Driver\TestDriver;
use Imi\ConnectionCenter\Test\Driver\TestDriverConfig;
use PHPUnit\Framework\TestCase;

class DriverTest extends TestCase
{
    public function testCreateDriver(): IConnectionDriver
    {
        $driver = new TestDriver(new ConnectionManagerConfig(TestDriver::class), new RandomLoadBalancer([
            TestDriverConfig::create(['test' => true]),
        ]));
        $this->assertTrue(true);

        return $driver;
    }

    /**
     * @depends testCreateDriver
     */
    public function testGetConnectionManagerConfig(IConnectionDriver $driver): void
    {
        $this->assertInstanceOf(ConnectionManagerConfig::class, $driver->getConnectionManagerConfig());
    }

    /**
     * @depends testCreateDriver
     */
    public function testGetConnectionLoadBalancer(IConnectionDriver $driver): void
    {
        $this->assertInstanceOf(IConnectionLoadBalancer::class, $driver->getConnectionLoadBalancer());
    }

    /**
     * @depends testCreateDriver
     */
    public function testCreateInstance(IConnectionDriver $driver): array
    {
        $instance = $driver->createInstance();
        $this->assertFalse($instance->connected);

        return [$driver, $instance];
    }

    public function testCreateInstanceFailed(): void
    {
        $driver = new TestDriver(new ConnectionManagerConfig(TestDriver::class), new RandomLoadBalancer([]));
        $this->expectExceptionMessage('No connection config available');
        $driver->createInstance();
    }

    /**
     * @depends testCreateInstance
     */
    public function testConnect(array $args): array
    {
        /**
         * @var IConnectionDriver $driver
         */
        /**
         * @var object $instance
         */
        [$driver, $instance] = $args;
        $this->assertFalse($instance->connected);
        $driver->connect($instance);
        $this->assertTrue($instance->connected);

        return $args;
    }

    /**
     * @depends testConnect
     */
    public function testReset(array $args): void
    {
        /**
         * @var IConnectionDriver $driver
         */
        /**
         * @var object $instance
         */
        [$driver, $instance] = $args;
        $this->assertFalse($instance->reseted);
        $driver->reset($instance);
        $this->assertTrue($instance->reseted);
    }

    /**
     * @depends testConnect
     */
    public function testCheckAvailable(array $args): void
    {
        /**
         * @var IConnectionDriver $driver
         */
        /**
         * @var object $instance
         */
        [$driver, $instance] = $args;
        $this->assertEquals(0, $instance->available);
        $driver->checkAvailable($instance);
        $this->assertEquals(1, $instance->available);
    }

    /**
     * @depends testConnect
     */
    public function testPing(array $args): void
    {
        /**
         * @var IConnectionDriver $driver
         */
        /**
         * @var object $instance
         */
        [$driver, $instance] = $args;
        $this->assertEquals(0, $instance->ping);
        $driver->ping($instance);
        $this->assertEquals(1, $instance->ping);
    }

    /**
     * @depends testConnect
     */
    public function testClose(array $args): void
    {
        /**
         * @var IConnectionDriver $driver
         */
        /**
         * @var object $instance
         */
        [$driver, $instance] = $args;
        $this->assertTrue($instance->connected);
        $driver->close($instance);
        $this->assertFalse($instance->connected);
    }
}
