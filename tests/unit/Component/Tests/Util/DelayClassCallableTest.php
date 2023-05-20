<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Test\Component\Bean\ReferenceBean;
use Imi\Util\DelayClassCallable;

class DelayClassCallableTest extends BaseTest
{
    public function testDelayClassCallable(): void
    {
        $callable = new DelayClassCallable(ReferenceBean::class, 'add');
        $this->assertEquals(ReferenceBean::class, $callable->getClassName());
        $this->assertEquals('add', $callable->getMethodName());
        $this->assertEquals(3, $callable(1, 2));
    }

    public function testDelayClassCallableReference(): void
    {
        $callable = new DelayClassCallable(ReferenceBean::class, 'testReturnValue');
        $this->assertEquals(ReferenceBean::class, $callable->getClassName());
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
        $callable = new DelayClassCallable(ReferenceBean::class, 'add');
        $str = serialize($callable);
        $this->assertNotEmpty($str);
        $callable2 = unserialize($str);
        $this->assertEquals(ReferenceBean::class, $callable2->getClassName());
        $this->assertEquals('add', $callable2->getMethodName());
        $this->assertEquals(3, $callable2(1, 2));
    }
}
