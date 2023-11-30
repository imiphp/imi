<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Test\BaseTest;
use Imi\Test\Component\Event\Classes\TestClassEvent;
use PHPUnit\Framework\Assert;

/**
 * @testdox Event
 */
class EventTest extends BaseTest
{
    public function testNormal(): void
    {
        Event::on('IMITEST.EVENT.A', function (EventParam|TestClassEvent $e): void {
            Assert::assertEquals('IMITEST.EVENT.A', $e->getEventName());
            Assert::assertEquals($this, $e->getTarget());
            if ($e instanceof EventParam)
            {
                $data = $e->getData();
                Assert::assertEquals('imi', $data['name']);
                $data['return'] = 19260817;
            }
            else
            {
                Assert::assertEquals('imi', $e->name);
                $e->return = 19260817;
            }
        });

        $return = null;
        Event::trigger('IMITEST.EVENT.A', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);
        Assert::assertEquals(19260817, $return);

        $event = new TestClassEvent('IMITEST.EVENT.A', $this, 'imi');
        Event::dispatch($event);
        Assert::assertEquals(19260817, $event->return);
    }

    public function testOne(): void
    {
        // trigger
        Event::one('IMITEST.EVENT.B', function (EventParam $e): void {
            Assert::assertEquals('IMITEST.EVENT.B', $e->getEventName());
            Assert::assertEquals($this, $e->getTarget());
            $data = $e->getData();
            Assert::assertEquals('imi', $data['name']);
            $data['return'] = 19260817;
        });

        $return = null;
        Event::trigger('IMITEST.EVENT.B', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);
        Assert::assertEquals(19260817, $return);

        $return = null;
        Event::trigger('IMITEST.EVENT.B', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);
        Assert::assertNull($return);

        // dispatch
        Event::one('IMITEST.EVENT.B', function (TestClassEvent $e): void {
            Assert::assertEquals('IMITEST.EVENT.B', $e->getEventName());
            Assert::assertEquals($this, $e->getTarget());
            Assert::assertEquals('imi', $e->name);
            $e->return = 19260817;
        });

        $event = new TestClassEvent('IMITEST.EVENT.B', $this, 'imi');
        Event::dispatch($event);
        Assert::assertEquals(19260817, $event->return);

        $event = new TestClassEvent('IMITEST.EVENT.B', $this, 'imi');
        Event::dispatch($event);
        Assert::assertNull($event->return);

        // 测试在事件中取消事件监听
        $return = null;
        Event::one('IMITEST.EVENT.OFF', static function () use (&$return): void {
            Event::off('IMITEST.EVENT.OFF');
            $return = 114514;
        });
        Event::dispatch(eventName: 'IMITEST.EVENT.OFF');
        Assert::assertEquals(114514, $return);
    }

    public function testOff(): void
    {
        $callable1 = static function (EventParam $e): void {
            Assert::assertTrue(false);
        };

        $callable2 = static function (EventParam $e): void {
            Assert::assertTrue(false);
        };
        Event::on('IMITEST.EVENT.C', $callable1);
        Event::one('IMITEST.EVENT.C', $callable2);

        Event::off('IMITEST.EVENT.C', $callable1);
        Event::off('IMITEST.EVENT.C', $callable2);

        $return = null;
        Event::trigger('IMITEST.EVENT.C', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);
        Assert::assertNull($return);

        $event = new TestClassEvent('IMITEST.EVENT.C', $this, 'imi');
        Event::dispatch($event);
        Assert::assertNull($event->return);
    }

    public function testListener(): void
    {
        $event = new TestClassEvent('IMITEST.EVENT.D', $this, 'imi');
        Event::dispatch($event);
        Assert::assertEquals(19260817, $event->return);
    }

    public function testClassListener1(): void
    {
        $testClass = new \Imi\Test\Component\Event\Classes\TestClass();
        $result = $testClass->test1();
        Assert::assertEquals(19260817, $result);
    }

    public function testClassListener2(): void
    {
        $testClass = new \Imi\Test\Component\Event\Classes\TestClass();
        $testClass->on('test2', static function (TestClassEvent $e) use ($testClass): void {
            Assert::assertEquals('test2', $e->getEventName());
            Assert::assertEquals($testClass, $e->getTarget());
            Assert::assertEquals('imi', $e->name);
            $e->return = 19260817;
        });
        $result = $testClass->test2();
        Assert::assertEquals(19260817, $result);
    }

    public function testClassListenerOff(): void
    {
        $testClass = new \Imi\Test\Component\Event\Classes\TestClass();
        $callable = static function (EventParam $e): void {
            Assert::assertTrue(false);
        };
        $testClass->on('test3', $callable);
        $testClass->off('test3', $callable);
        $result = $testClass->test3();
        Assert::assertNull($result);
    }
}
