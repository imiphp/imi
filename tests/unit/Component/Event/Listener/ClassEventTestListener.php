<?php

namespace Imi\Test\Component\Event\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Test\Component\Event\Classes\TestClass;
use PHPUnit\Framework\Assert;

/**
 * @ClassEventListener(className="Imi\Test\Component\Event\Classes\TestClass", eventName="test1")
 */
class ClassEventTestListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param \Imi\Event\EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        Assert::assertEquals('test1', $e->getEventName());
        Assert::assertEquals(TestClass::class, \get_class($e->getTarget()));
        $data = $e->getData();
        Assert::assertEquals('imi', $data['name']);
        $data['return'] = 19260817;
    }
}
