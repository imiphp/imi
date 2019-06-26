<?php
namespace Imi\Test\Tests;

use Imi\Event\Event;
use Imi\Test\BaseTest;
use Imi\Event\EventParam;
use PHPUnit\Framework\Assert;

class EventTest extends BaseTest
{
    public function testNormal()
    {
        Event::on('IMITEST.EVENT.A', function(EventParam $e){
            Assert::assertEquals('IMITEST.EVENT.A', $e->getEventName());
            Assert::assertEquals($this, $e->getTarget());
            $data = $e->getData();
            Assert::assertEquals('imi', $data['name']);
            $data['return'] = 19260817;
        });

        $return = null;
        Event::trigger('IMITEST.EVENT.A', [
            'name'  =>  'imi',
            'return'=>  &$return,
        ], $this);
        Assert::assertEquals(19260817, $return);
    }

    public function testOne()
    {
        Event::one('IMITEST.EVENT.B', function(EventParam $e){
            Assert::assertEquals('IMITEST.EVENT.B', $e->getEventName());
            Assert::assertEquals($this, $e->getTarget());
            $data = $e->getData();
            Assert::assertEquals('imi', $data['name']);
            $data['return'] = 19260817;
        });

        $return = null;
        Event::trigger('IMITEST.EVENT.B', [
            'name'  =>  'imi',
            'return'=>  &$return,
        ], $this);
        Assert::assertEquals(19260817, $return);

        $return = null;
        Event::trigger('IMITEST.EVENT.B', [
            'name'  =>  'imi',
            'return'=>  &$return,
        ], $this);
        Assert::assertNull($return);
    }

    public function testOff()
    {
        $callable1 = function(EventParam $e){
            Assert::assertTrue(false);
        };

        $callable2 = function(EventParam $e){
            Assert::assertTrue(false);
        };
        Event::on('IMITEST.EVENT.C', $callable1);
        Event::one('IMITEST.EVENT.C', $callable2);

        Event::off('IMITEST.EVENT.C', $callable1);
        Event::off('IMITEST.EVENT.C', $callable2);

        $return = null;
        Event::trigger('IMITEST.EVENT.C', [
            'name'  =>  'imi',
            'return'=>  &$return,
        ], $this);
        Assert::assertNull($return);
    }

}
