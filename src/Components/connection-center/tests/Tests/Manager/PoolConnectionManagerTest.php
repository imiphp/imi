<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Tests\Manager;

use Imi\App;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Enum\ConnectionStatus;
use Imi\ConnectionCenter\Handler\AlwaysNew\AlwaysNewConnectionManager;
use Imi\ConnectionCenter\Handler\Pool\PoolConnectionManager;
use Imi\ConnectionCenter\Test\Driver\TestDriver;
use PHPUnit\Framework\TestCase;

use function Imi\env;
use function Yurun\Swoole\Coroutine\goWait;

class PoolConnectionManagerTest extends TestCase
{
    public const EXCEPTION_MESSAGE_CLOSED = 'Connection manager is unavailable';

    protected function setUp(): void
    {
        if ('swoole' !== env('CONNECTION_CENTER_TEST_MODE'))
        {
            $this->markTestSkipped();
        }
    }

    public function testInvalidConfig(): void
    {
        $this->expectExceptionMessageMatches('/PoolConnectionManager__Bean__\d+ require Imi\\\ConnectionCenter\\\Handler\\\Pool\\\PoolConnectionManagerConfig, but Imi\\\ConnectionCenter\\\Handler\\\AlwaysNew\\\AlwaysNewConnectionManagerConfig/');
        App::newInstance(PoolConnectionManager::class, AlwaysNewConnectionManager::createConfig(['driver' => TestDriver::class]));
    }

    public function testCreateConnectionManager(bool $enableStatistics = true): PoolConnectionManager
    {
        $connectionManager = App::newInstance(PoolConnectionManager::class, PoolConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => $enableStatistics, 'resources' => [['test' => true]]]));

        $this->assertTrue($connectionManager->isAvailable());

        return $connectionManager;
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testCreateConnection(PoolConnectionManager $connectionManager): void
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
    public function testGetConnection(PoolConnectionManager $connectionManager): IConnection
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
    public function testReleaseConnectionException(PoolConnectionManager $connectionManager): void
    {
        $connection = $this->testGetConnection($connectionManager);
        $this->expectExceptionMessage('Connection is not in wait release status');
        $connectionManager->releaseConnection($connection);
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testReleaseConnection(PoolConnectionManager $connectionManager): void
    {
        $connection = $this->testGetConnection($connectionManager);
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $instance = $connection->getInstance();
        $connection->release();
        $this->assertEquals(ConnectionStatus::Unavailable, $connection->getStatus());
        $this->assertEquals($connectionManager, $connection->getManager());
        $this->assertInstanceOf(\stdClass::class, $instance);
        $this->assertTrue($instance->config->getTest());
        $this->assertTrue($instance->connected);
        $this->assertTrue($instance->reseted);
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
        $this->assertEquals(1, $statistics->getTotalConnectionCount()); // 默认的 minResources=1
        $this->assertEquals(1, $statistics->getFreeConnectionCount());  // 默认的 minResources=1
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
        $this->assertEquals(1, $statistics->getTotalConnectionCount());
        $this->assertEquals(1, $statistics->getFreeConnectionCount());
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
        $this->assertEquals(1, $statistics->getTotalConnectionCount());
        $this->assertEquals(1, $statistics->getFreeConnectionCount());
        $this->assertEquals(0, $statistics->getUsedConnectionCount());
        $this->assertEquals(0, $statistics->getMaxGetConnectionTime());
        $this->assertEquals(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());
        $this->assertEquals(0, $statistics->getLastGetConnectionTime());

        // 获取连接
        $connection = $connectionManager->getConnection();
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(1, $statistics->getCreateConnectionTimes());
        $this->assertEquals(1, $statistics->getGetConnectionTimes());                   // 改变
        $this->assertEquals(1, $statistics->getReleaseConnectionTimes());
        $this->assertEquals(1, $statistics->getTotalConnectionCount());
        $this->assertEquals(0, $statistics->getFreeConnectionCount());                  // 改变
        $this->assertEquals(1, $statistics->getUsedConnectionCount());                  // 改变
        $this->assertGreaterThan(0, $statistics->getMaxGetConnectionTime());            // 改变
        $this->assertLessThan(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());  // 改变
        $this->assertGreaterThan(0, $statistics->getLastGetConnectionTime());           // 改变

        // 释放连接
        $connection->release();
        $statistics = $connectionManager->getStatistics();
        $this->assertEquals(1, $statistics->getCreateConnectionTimes());
        $this->assertEquals(1, $statistics->getGetConnectionTimes());
        $this->assertEquals(2, $statistics->getReleaseConnectionTimes());   // 改变
        $this->assertEquals(1, $statistics->getTotalConnectionCount());
        $this->assertEquals(1, $statistics->getFreeConnectionCount());      // 改变
        $this->assertEquals(0, $statistics->getUsedConnectionCount());      // 改变
        $this->assertGreaterThan(0, $statistics->getMaxGetConnectionTime());
        $this->assertLessThan(\PHP_FLOAT_MAX, $statistics->getMinGetConnectionTime());
        $this->assertGreaterThan(0, $statistics->getLastGetConnectionTime());

        $this->assertStringMatchesFormat('{"createConnectionTimes":1,"getConnectionTimes":1,"releaseConnectionTimes":2,"totalConnectionCount":1,"freeConnectionCount":1,"usedConnectionCount":0,"maxGetConnectionTime":%f,"minGetConnectionTime":%f,"lastGetConnectionTime":%f}', json_encode($statistics));
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

    public function testCloseFailed(): void
    {
        $connectionManager = $this->testCreateConnectionManager();
        $connectionManager->close();
        $this->expectExceptionMessage('Connection manager is unavailable');
        $connectionManager->close();
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
        $this->assertEquals(2, $statistics->getTotalConnectionCount());
    }

    public function testGCMaxActiveTime(): void
    {
        $connectionManager = App::newInstance(PoolConnectionManager::class, PoolConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => true, 'pool' => [
            'gcInterval'    => 1,
            'maxActiveTime' => 1,
        ], 'resources' => [['test' => true]]]));

        $this->assertTrue($connectionManager->isAvailable());

        $connection = $connectionManager->getConnection();
        $instance = $connection->getInstance();
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $this->assertTrue($instance->connected);

        $connection->release();

        // 每 1s 触发一次，防止第一次达不到 GC 条件，执行 2 次
        for ($i = 0; $i < 2; ++$i)
        {
            sleep(1); // 等待触发
            // @phpstan-ignore-next-line
            if (!$instance->connected)
            {
                break;
            }
        }

        $this->assertEquals(ConnectionStatus::Unavailable, $connection->getStatus());
        $this->assertFalse($instance->connected);

        $connectionManager->close();
    }

    public function testGCMaxIdleTime(): void
    {
        $connectionManager = App::newInstance(PoolConnectionManager::class, PoolConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => true, 'pool' => [
            'minResources' => 0,
            'gcInterval'   => 1,
            'maxIdleTime'  => 1,
        ], 'resources' => [['test' => true]]]));

        $this->assertTrue($connectionManager->isAvailable());

        $connection = $connectionManager->getConnection();
        $instance = $connection->getInstance();
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $this->assertTrue($instance->connected);

        $connection->release();

        // 每 1s 触发一次，防止第一次达不到 GC 条件，执行 2 次
        for ($i = 0; $i < 2; ++$i)
        {
            sleep(1); // 等待触发
            // @phpstan-ignore-next-line
            if (!$instance->connected)
            {
                break;
            }
        }

        $this->assertEquals(ConnectionStatus::Unavailable, $connection->getStatus());
        $this->assertFalse($instance->connected);

        $connectionManager->close();
    }

    public function testGCMaxUsedTime(): void
    {
        $connectionManager = App::newInstance(PoolConnectionManager::class, PoolConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => true, 'pool' => [
            'gcInterval'   => 1,
            'maxUsedTime'  => 1,
        ], 'resources' => [['test' => true]]]));

        $this->assertTrue($connectionManager->isAvailable());

        $connection = $connectionManager->getConnection();
        $instance = $connection->getInstance();
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $this->assertTrue($instance->connected);

        // 每 1s 触发一次，防止第一次达不到 GC 条件，执行 2 次
        for ($i = 0; $i < 2; ++$i)
        {
            sleep(1); // 等待触发
            // @phpstan-ignore-next-line
            if (!$instance->connected)
            {
                break;
            }
        }
        $this->assertEquals(ConnectionStatus::Unavailable, $connection->getStatus());
        $this->assertFalse($instance->connected);

        $connectionManager->close();
    }

    public function testCheckStateWhenGetResource(): void
    {
        $connectionManager = App::newInstance(PoolConnectionManager::class, PoolConnectionManager::createConfig([
            'driver'                    => TestDriver::class,
            'enableStatistics'          => true,
            'resources'                 => [['test' => true]],
            'checkStateWhenGetResource' => true,
        ]));

        $this->assertTrue($connectionManager->isAvailable());

        $connection = $connectionManager->getConnection();
        $instance = $connection->getInstance();
        $this->assertEquals(1, $instance->available);

        // 测试重连
        $connection->release();
        $instance->connected = false;
        $connection = $connectionManager->getConnection();
        $instance = $connection->getInstance();
        $this->assertTrue($instance->connected);
        $this->assertEquals(2, $instance->available);

        $connectionManager->close();
    }

    public function testHeartbeat(): void
    {
        $connectionManager = App::newInstance(PoolConnectionManager::class, PoolConnectionManager::createConfig([
            'driver'                    => TestDriver::class,
            'enableStatistics'          => true,
            'resources'                 => [['test' => true]],
            'pool'                      => [
                'heartbeatInterval' => 1,
            ],
        ]));
        $this->assertTrue($connectionManager->isAvailable());

        $connection = $connectionManager->getConnection();
        $instance = $connection->getInstance();
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $this->assertTrue($instance->connected);
        $this->assertEquals(0, $instance->ping);

        // 被占用的连接不触发心跳
        for ($i = 0; $i < 2; ++$i)
        {
            sleep(1); // 等待触发
            // @phpstan-ignore-next-line
            if ($instance->ping > 0)
            {
                break;
            }
        }
        $this->assertEquals(0, $instance->ping);

        // 空闲连接触发心跳
        $connection->release();
        for ($i = 0; $i < 2; ++$i)
        {
            sleep(1); // 等待触发
            // @phpstan-ignore-next-line
            if ($instance->ping > 0)
            {
                break;
            }
        }
        $this->assertGreaterThan(0, $instance->ping);

        $connectionManager->close();
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testGetInstanceAfterRelease(PoolConnectionManager $connectionManager): void
    {
        $connection = $this->testGetConnection($connectionManager);
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $connection->release();

        $this->expectExceptionMessage('Connection is not available');
        $connection->getInstance();
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testReleaseAfterRelease(PoolConnectionManager $connectionManager): void
    {
        $connection = $this->testGetConnection($connectionManager);
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $connection->release();

        $this->expectExceptionMessage('Connection is not available');
        $connection->release();
    }

    /**
     * @depends testCreateConnectionManager
     */
    public function testDetachAfterRelease(PoolConnectionManager $connectionManager): void
    {
        $connection = $this->testGetConnection($connectionManager);
        $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        $connection->release();

        $this->expectExceptionMessage('Connection is not available');
        $connection->detach();
    }
}
