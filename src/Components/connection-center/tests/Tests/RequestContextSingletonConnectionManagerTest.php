<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Tests;

use Imi\App;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Enum\ConnectionStatus;
use Imi\ConnectionCenter\Handler\RequestContextSingleton\RequestContextSingletonConnectionManager;
use Imi\ConnectionCenter\Test\Driver\TestDriver;
use PHPUnit\Framework\TestCase;

use function Imi\env;
use function Yurun\Swoole\Coroutine\goWait;

class RequestContextSingletonConnectionManagerTest extends TestCase
{
    public const EXCEPTION_MESSAGE_CLOSED = 'Connection manager is unavailable';

    public function testCreateConnectionManager(bool $enableStatistics = true): RequestContextSingletonConnectionManager
    {
        $connectionManager = App::newInstance(RequestContextSingletonConnectionManager::class, RequestContextSingletonConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => $enableStatistics, 'resource' => ['test' => true]]));

        $this->assertTrue($connectionManager->isAvailable());

        return $connectionManager;
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testCreateConnection(RequestContextSingletonConnectionManager $connectionManager): void
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
    public function testGetConnection(RequestContextSingletonConnectionManager $connectionManager): IConnection
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
    public function testReleaseConnectionException(RequestContextSingletonConnectionManager $connectionManager): void
    {
        $connection = $this->testGetConnection($connectionManager);
        $this->expectExceptionMessage('Connection is not in wait release status');
        $connectionManager->releaseConnection($connection);
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testReleaseConnection(RequestContextSingletonConnectionManager $connectionManager): void
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
        $this->assertEquals(1, $statistics->getTotalConnectionCount());                 // 改变
        $this->assertEquals(0, $statistics->getFreeConnectionCount());
        $this->assertEquals(1, $statistics->getUsedConnectionCount());                  // 改变
        $this->assertGreaterThan(0, $statistics->getMaxGetConnectionTime());            // 改变
        $this->assertLessThan(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());  // 改变
        $this->assertGreaterThan(0, $statistics->getLastGetConnectionTime());           // 改变

        // 释放连接
        $connection->release();
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(2, $statistics->getCreateConnectionTimes());
        $this->assertEquals(1, $statistics->getGetConnectionTimes());
        $this->assertEquals(2, $statistics->getReleaseConnectionTimes());   // 改变
        $this->assertEquals(0, $statistics->getTotalConnectionCount());     // 改变
        $this->assertEquals(0, $statistics->getFreeConnectionCount());
        $this->assertEquals(0, $statistics->getUsedConnectionCount());      // 改变
        $this->assertGreaterThan(0, $statistics->getMaxGetConnectionTime());
        $this->assertLessThan(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());
        $this->assertGreaterThan(0, $statistics->getLastGetConnectionTime());
    }

    public function testClose(): void
    {
        $connectionManager = $this->testCreateConnectionManager();
        $connectionByCreate = $connectionManager->createConnection();
        $this->assertEquals(ConnectionStatus::Available, $connectionByCreate->getStatus());
        $connectionByGet = $connectionManager->getConnection();
        $this->assertEquals(ConnectionStatus::Available, $connectionByGet->getStatus());
        $connectionManager->close();
        // 关闭连接管理器，创建的连接不受影响
        $this->assertEquals(ConnectionStatus::Available, $connectionByCreate->getStatus());
        // 关闭连接管理器，自动关闭所有连接
        $this->assertEquals(ConnectionStatus::Unavailable, $connectionByGet->getStatus());

        try
        {
            $connectionManager->createConnection();
            $this->assertTrue(false);
        }
        catch (\RuntimeException $re)
        {
            $this->assertEquals(self::EXCEPTION_MESSAGE_CLOSED, $re->getMessage());
        }

        try
        {
            $connectionManager->getConnection();
            $this->assertTrue(false);
        }
        catch (\RuntimeException $re)
        {
            $this->assertEquals(self::EXCEPTION_MESSAGE_CLOSED, $re->getMessage());
        }
    }

    public function testDetach(): void
    {
        $connectionManager = $this->testCreateConnectionManager();
        $connection = $connectionManager->getConnection();
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $connection->detach();
        $connectionManager->close();
        // 关闭连接管理器，已分离连接不受影响
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
    }

    public function testRequestContextDestory(): void
    {
        if ('swoole' !== env('CONNECTION_CENTER_TEST_MODE'))
        {
            $this->markTestSkipped();
        }
        $connectionManager = $this->testCreateConnectionManager();
        $connectionOut = $connectionManager->getConnection();
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(1, $statistics->getTotalConnectionCount());
        goWait(function () use ($connectionManager, $connectionOut): void {
            $connection = $connectionManager->getConnection();
            $this->assertTrue($connectionOut !== $connection);
            $statistics = $connectionManager->getStatistics();
            $this->assertEquals(2, $statistics->getTotalConnectionCount());
        }, -1, true);
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(1, $statistics->getTotalConnectionCount());
    }

    public function testCheckStateWhenGetResource(): void
    {
        $connectionManager = App::newInstance(RequestContextSingletonConnectionManager::class, RequestContextSingletonConnectionManager::createConfig([
            'driver'                    => TestDriver::class,
            'enableStatistics'          => true,
            'resource'                  => ['test' => true],
            'checkStateWhenGetResource' => true,
        ]));

        $this->assertTrue($connectionManager->isAvailable());

        $connection = $connectionManager->getConnection();
        $instance = $connection->getInstance();
        $this->assertEquals(0, $instance->available);

        $connection = $connectionManager->getConnection();
        $instance = $connection->getInstance();
        $this->assertEquals(1, $instance->available);
    }
}
