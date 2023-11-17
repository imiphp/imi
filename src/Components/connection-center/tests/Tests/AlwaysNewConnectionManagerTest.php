<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Tests;

use Imi\App;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Enum\ConnectionStatus;
use Imi\ConnectionCenter\Handler\AlwaysNew\AlwaysNewConnectionManager;
use Imi\ConnectionCenter\Test\Driver\TestDriver;
use PHPUnit\Framework\TestCase;

class AlwaysNewConnectionManagerTest extends TestCase
{
    public function testCreateConnectionManager(bool $enableStatistics = true): AlwaysNewConnectionManager
    {
        $connectionManager = App::newInstance(AlwaysNewConnectionManager::class, AlwaysNewConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => $enableStatistics, 'test' => true]));

        $this->assertTrue($connectionManager->isAvailable());

        return $connectionManager;
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testCreateConnection(AlwaysNewConnectionManager $connectionManager): void
    {
        $connection = $connectionManager->createConnection();
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $this->assertEquals($connectionManager, $connection->getManager());
        $instance = $connection->getInstance();
        $this->assertInstanceOf(\stdClass::class, $instance);
        $this->assertTrue($instance->config->getTest());
        $this->assertTrue($instance->connected);
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testGetConnection(AlwaysNewConnectionManager $connectionManager): IConnection
    {
        $connection = $connectionManager->getConnection();
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $this->assertEquals($connectionManager, $connection->getManager());
        $instance = $connection->getInstance();
        $this->assertInstanceOf(\stdClass::class, $instance);
        $this->assertTrue($instance->config->getTest());
        $this->assertTrue($instance->connected);

        return $connection;
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testReleaseConnectionException(AlwaysNewConnectionManager $connectionManager): void
    {
        $connection = $this->testGetConnection($connectionManager);
        $this->expectExceptionMessage('Connection is not in wait release status');
        $connectionManager->releaseConnection($connection);
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testReleaseConnection(AlwaysNewConnectionManager $connectionManager): void
    {
        $connection = $this->testGetConnection($connectionManager);
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $instance = $connection->getInstance();
        $connection->release();
        $this->assertEquals(ConnectionStatus::Unavailable, $connection->getStatus());
        $this->assertEquals($connectionManager, $connection->getManager());
        $this->assertInstanceOf(\stdClass::class, $instance);
        $this->assertTrue($instance->config->getTest());
        $this->assertFalse($instance->connected);
    }

    public function testGetStatisticsDisabled(): void
    {
        $connectionManager = $this->testCreateConnectionManager(false);
        $this->expectExceptionMessage('Connection manager statistics is disabled');
        $connectionManager->getStatistics();
    }

    public function testGetStatistics(): void
    {
        $connectionManager = $this->testCreateConnectionManager();
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(0, $statistics->getCreateConnectionTimes());
        $this->assertEquals(0, $statistics->getGetConnectionTimes());
        $this->assertEquals(0, $statistics->getReleaseConnectionTimes());
        $this->assertEquals(0, $statistics->getTotalConnectionCount());
        $this->assertEquals(0, $statistics->getFreeConnectionCount());
        $this->assertEquals(0, $statistics->getUsedConnectionCount());
        $this->assertEquals(0, $statistics->getMaxGetConnectionTime());
        $this->assertEquals(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());
        $this->assertEquals(0, $statistics->getLastGetConnectionTime());

        // 创建连接
        $connection = $connectionManager->createConnection();
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(1, $statistics->getCreateConnectionTimes()); // 改变
        $this->assertEquals(0, $statistics->getGetConnectionTimes());
        $this->assertEquals(0, $statistics->getReleaseConnectionTimes());
        $this->assertEquals(0, $statistics->getTotalConnectionCount());
        $this->assertEquals(0, $statistics->getFreeConnectionCount());
        $this->assertEquals(0, $statistics->getUsedConnectionCount());
        $this->assertEquals(0, $statistics->getMaxGetConnectionTime());
        $this->assertEquals(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());
        $this->assertEquals(0, $statistics->getLastGetConnectionTime());

        // 析构自动释放
        $connection = null;
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(1, $statistics->getCreateConnectionTimes());
        $this->assertEquals(0, $statistics->getGetConnectionTimes());
        $this->assertEquals(1, $statistics->getReleaseConnectionTimes()); // 改变
        $this->assertEquals(0, $statistics->getTotalConnectionCount());
        $this->assertEquals(0, $statistics->getFreeConnectionCount());
        $this->assertEquals(0, $statistics->getUsedConnectionCount());
        $this->assertEquals(0, $statistics->getMaxGetConnectionTime());
        $this->assertEquals(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());
        $this->assertEquals(0, $statistics->getLastGetConnectionTime());

        // 获取连接
        $connection = $connectionManager->getConnection();
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(2, $statistics->getCreateConnectionTimes());                // 改变
        $this->assertEquals(1, $statistics->getGetConnectionTimes());                   // 改变
        $this->assertEquals(1, $statistics->getReleaseConnectionTimes());
        $this->assertEquals(0, $statistics->getTotalConnectionCount());
        $this->assertEquals(0, $statistics->getFreeConnectionCount());
        $this->assertEquals(0, $statistics->getUsedConnectionCount());
        $this->assertGreaterThan(0, $statistics->getMaxGetConnectionTime());            // 改变
        $this->assertLessThan(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());  // 改变
        $this->assertGreaterThan(0, $statistics->getLastGetConnectionTime());           // 改变

        // 释放连接
        $connection->release();
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(2, $statistics->getCreateConnectionTimes());
        $this->assertEquals(1, $statistics->getGetConnectionTimes());
        $this->assertEquals(2, $statistics->getReleaseConnectionTimes()); // 改变
        $this->assertEquals(0, $statistics->getTotalConnectionCount());
        $this->assertEquals(0, $statistics->getFreeConnectionCount());
        $this->assertEquals(0, $statistics->getUsedConnectionCount());
        $this->assertGreaterThan(0, $statistics->getMaxGetConnectionTime());
        $this->assertLessThan(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());
        $this->assertGreaterThan(0, $statistics->getLastGetConnectionTime());
    }
}
