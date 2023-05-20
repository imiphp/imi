<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\DelayBeanCallable;

class DelayBeanCallableTest extends BaseTest
{
    public function testDelayBeanCallable(): void
    {
        $callable = new DelayBeanCallable('ReferenceBean', 'add');
        $this->assertEquals('ReferenceBean', $callable->getBeanName());
        $this->assertEquals('add', $callable->getMethodName());
        $this->assertEquals(3, $callable(1, 2));
    }

    public function testDelayBeanCallableReference(): void
    {
        $callable = new DelayBeanCallable('ReferenceBean', 'testReturnValue', [__METHOD__]);
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
        $callable = new DelayBeanCallable('ReferenceBean', 'add');
        $str = serialize($callable);
        $this->assertNotEmpty($str);
        $callable2 = unserialize($str);
        $this->assertEquals('ReferenceBean', $callable2->getBeanName());
        $this->assertEquals('add', $callable2->getMethodName());
        $this->assertEquals(3, $callable2(1, 2));
    }
}
