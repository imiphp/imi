<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Test\Tests;

use Imi\App;
use Imi\ConnectionCenter\ConnectionCenter;
use Imi\ConnectionCenter\Enum\ConnectionStatus;
use Imi\ConnectionCenter\Handler\AlwaysNew\AlwaysNewConnectionManager;
use Imi\ConnectionCenter\Handler\Pool\PoolConnectionManager;
use Imi\ConnectionCenter\Handler\RequestContextSingleton\RequestContextSingletonConnectionManager;
use Imi\ConnectionCenter\Handler\Singleton\SingletonConnectionManager;
use Imi\ConnectionCenter\Test\Driver\TestDriver;
use Imi\RequestContext;
use PHPUnit\Framework\TestCase;

use function Imi\env;

class ConnectionCenterTest extends TestCase
{
    protected static ConnectionCenter $connectionCenter;

    public function testNewInstance(): void
    {
        self::$connectionCenter = App::newInstance(ConnectionCenter::class);
        $this->assertTrue(true);
    }

    /**
     * @depends testNewInstance
     */
    public function testAddConnectionManager(): void
    {
        self::$connectionCenter->addConnectionManager('alwaysNew', AlwaysNewConnectionManager::class, AlwaysNewConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => true, 'resource' => ['test' => true], 'checkStateWhenGetResource' => true]));

        self::$connectionCenter->addConnectionManager('requestContextSingleton', RequestContextSingletonConnectionManager::class, RequestContextSingletonConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => true, 'resource' => ['test' => true], 'checkStateWhenGetResource' => true]));

        self::$connectionCenter->addConnectionManager('singleton', SingletonConnectionManager::class, SingletonConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => true, 'resource' => ['test' => true], 'checkStateWhenGetResource' => true]));

        if ('swoole' === env('CONNECTION_CENTER_TEST_MODE'))
        {
            self::$connectionCenter->addConnectionManager('pool', PoolConnectionManager::class, PoolConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => true, 'resource' => ['test' => true], 'checkStateWhenGetResource' => true]));
        }

        $this->assertTrue(true);
    }

    /**
     * @depends testAddConnectionManager
     */
    public function testGetConnectionManagers(): void
    {
        $this->assertEquals('swoole' === env('CONNECTION_CENTER_TEST_MODE') ? 4 : 3, \count(self::$connectionCenter->getConnectionManagers()));
    }

    /**
     * @depends testAddConnectionManager
     */
    public function testHasConnectionManager(): void
    {
        $this->assertTrue(self::$connectionCenter->hasConnectionManager('alwaysNew'));
        $this->assertTrue(self::$connectionCenter->hasConnectionManager('requestContextSingleton'));
        $this->assertTrue(self::$connectionCenter->hasConnectionManager('singleton'));
        if ('swoole' === env('CONNECTION_CENTER_TEST_MODE'))
        {
            $this->assertTrue(self::$connectionCenter->hasConnectionManager('pool'));
        }

        $this->assertFalse(self::$connectionCenter->hasConnectionManager('notFound'));
    }

    /**
     * @depends testAddConnectionManager
     */
    public function testRemoveConnectionManager(): void
    {
        self::$connectionCenter->addConnectionManager('test', AlwaysNewConnectionManager::class, AlwaysNewConnectionManager::createConfig(['driver' => TestDriver::class, 'enableStatistics' => true, 'resource' => ['test' => true]]));
        $this->assertTrue(self::$connectionCenter->hasConnectionManager('test'));
        self::$connectionCenter->removeConnectionManager('test');
        $this->assertFalse(self::$connectionCenter->hasConnectionManager('test'));

        $this->expectExceptionMessage('Connection manager test does not exists');
        self::$connectionCenter->removeConnectionManager('test');
    }

    /**
     * @depends testAddConnectionManager
     */
    public function testGetConnectionManager(): void
    {
        $this->assertInstanceOf(AlwaysNewConnectionManager::class, self::$connectionCenter->getConnectionManager('alwaysNew'));

        $this->assertInstanceOf(RequestContextSingletonConnectionManager::class, self::$connectionCenter->getConnectionManager('requestContextSingleton'));

        $this->assertInstanceOf(SingletonConnectionManager::class, self::$connectionCenter->getConnectionManager('singleton'));

        if ('swoole' === env('CONNECTION_CENTER_TEST_MODE'))
        {
            $this->assertInstanceOf(PoolConnectionManager::class, self::$connectionCenter->getConnectionManager('pool'));
        }
    }

    /**
     * @depends testAddConnectionManager
     */
    public function testGetConnection(): void
    {
        $names = [
            'alwaysNew',
            'requestContextSingleton',
            'singleton',
        ];
        if ('swoole' === env('CONNECTION_CENTER_TEST_MODE'))
        {
            $names[] = 'pool';
        }
        foreach ($names as $name)
        {
            $connection = self::$connectionCenter->getConnection($name);
            $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        }
    }

    /**
     * @depends testAddConnectionManager
     */
    public function testGetRequestContextConnection(): void
    {
        $names = [
            'alwaysNew',
            'requestContextSingleton',
            'singleton',
        ];
        if ('swoole' === env('CONNECTION_CENTER_TEST_MODE'))
        {
            $names[] = 'pool';
        }
        foreach ($names as $name)
        {
            $connection = self::$connectionCenter->getRequestContextConnection($name);
            $this->assertEquals(ConnectionStatus::Available, $connection->getStatus());
        }

        // 连接上下文销毁释放测试

        // 请求上下文单例
        if (RequestContext::exists(RequestContext::getCurrentId()))
        {
            RequestContext::destroy();
            RequestContext::create();
        }
        $manager = self::$connectionCenter->getConnectionManager('requestContextSingleton');
        $connection = self::$connectionCenter->getRequestContextConnection('requestContextSingleton');
        $this->assertEquals(1, $manager->getStatistics()->getTotalConnectionCount());
        RequestContext::destroy();
        RequestContext::create();
        $this->assertEquals(ConnectionStatus::Unavailable, $connection->getStatus());
        $this->assertEquals(0, $manager->getStatistics()->getTotalConnectionCount());

        // 连接池
        if ('swoole' === env('CONNECTION_CENTER_TEST_MODE'))
        {
            if (RequestContext::exists(RequestContext::getCurrentId()))
            {
                RequestContext::destroy();
                RequestContext::create();
            }
            $manager = self::$connectionCenter->getConnectionManager('pool');
            $connection = self::$connectionCenter->getRequestContextConnection('pool');
            $this->assertEquals(0, $manager->getStatistics()->getFreeConnectionCount());
            RequestContext::destroy();
            RequestContext::create();
            $this->assertEquals(ConnectionStatus::Unavailable, $connection->getStatus());
            $this->assertEquals(1, $manager->getStatistics()->getFreeConnectionCount());
        }
    }

    /**
     * @depends testNewInstance
     */
    public function testCloseAllConnectionManager(): void
    {
        self::$connectionCenter->closeAllConnectionManager();
        $this->assertTrue(true);
    }
}
