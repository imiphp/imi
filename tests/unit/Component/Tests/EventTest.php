<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

/**
 * @testdox Event
 */
class EventTest extends BaseTest
{
    public function testNormal(): void
    {
        Event::on('IMITEST.EVENT.A', function (EventParam $e) {
            Assert::assertEquals('IMITEST.EVENT.A', $e->getEventName());
            Assert::assertEquals($this, $e->getTarget());
            $data = $e->getData();
            Assert::assertEquals('imi', $data['name']);
            $data['return'] = 19260817;
        });

        $return = null;
        Event::trigger('IMITEST.EVENT.A', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);
        Assert::assertEquals(19260817, $return);
    }

    public function testOne(): void
    {
        Event::one('IMITEST.EVENT.B', function (EventParam $e) {
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
    }

    public function testOff(): void
    {
        $callable1 = function (EventParam $e) {
            Assert::assertTrue(false);
        };

        $callable2 = function (EventParam $e) {
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
    }

    public function testListener(): void
    {
        $return = null;
        Event::trigger('IMITEST.EVENT.D', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);
        Assert::assertEquals(19260817, $return);
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
        $testClass->on('test2', function (EventParam $e) use ($testClass) {
            Assert::assertEquals('test2', $e->getEventName());
            Assert::assertEquals($testClass, $e->getTarget());
            $data = $e->getData();
            Assert::assertEquals('imi', $data['name']);
            $data['return'] = 19260817;
        });
        $result = $testClass->test2();
        Assert::assertEquals(19260817, $result);
    }

    public function testClassListenerOff(): void
    {
        $testClass = new \Imi\Test\Component\Event\Classes\TestClass();
        $callable = function (EventParam $e) {
            Assert::assertTrue(false);
        };
        $testClass->on('test3', $callable);
        $testClass->off('test3', $callable);
        $result = $testClass->test3();
        Assert::assertNull($result);
    }
}
