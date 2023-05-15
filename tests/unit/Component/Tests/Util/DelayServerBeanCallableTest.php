<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\RequestContext;
use Imi\Server\ServerManager;
use Imi\Test\BaseTest;
use Imi\Test\Component\Server\TestServer;
use Imi\Util\DelayServerBeanCallable;

class DelayServerBeanCallableTest extends BaseTest
{
    public static function setUpBeforeClass(): void
    {
        $server = ServerManager::createServer(__CLASS__, ['type' => TestServer::class]);
        RequestContext::set('server', $server);
    }

    public static function tearDownAfterClass(): void
    {
        RequestContext::unset('server');
    }

    public function testDelayServerBeanCallable(): void
    {
        $callable = new DelayServerBeanCallable(__CLASS__, 'ReferenceBean', 'add');
        $this->assertEquals('ReferenceBean', $callable->getBeanName());
        $this->assertEquals('add', $callable->getMethodName());
        $this->assertEquals(3, $callable(1, 2));
    }

    public function testDelayServerBeanCallableReference(): void
    {
        $callable = new DelayServerBeanCallable(__CLASS__, 'ReferenceBean', 'testReturnValue', [__METHOD__]);
        $this->assertEquals('ReferenceBean', $callable->getBeanName());
        $this->assertEquals('testReturnValue', $callable->getMethodName());
        $callable()[] = 1;
        $list = &$callable();
        $this->assertEquals([1], $list);
        $list[] = 2;
        $list = $callable();
        $this->assertEquals([1, 2], $list);
    }

    public function testSerialize(): void
    {
        $callable = new DelayServerBeanCallable(__CLASS__, 'ReferenceBean', 'add');
        $str = serialize($callable);
        $this->assertNotEmpty($str);
        $callable2 = unserialize($str);
        $this->assertEquals('ReferenceBean', $callable2->getBeanName());
        $this->assertEquals('add', $callable2->getMethodName());
        $this->assertEquals(3, $callable2(1, 2));
    }
}
