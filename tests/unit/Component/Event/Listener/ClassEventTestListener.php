<?php

declare(strict_types=1);

namespace Imi\Test\Component\Event\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Event\IEventListener;
use Imi\Test\Component\Event\Classes\TestClass;
use Imi\Test\Component\Event\Classes\TestClassEvent;
use PHPUnit\Framework\Assert;

#[ClassEventListener(className: \Imi\Test\Component\Event\Classes\TestClass::class, eventName: 'test1')]
class ClassEventTestListener implements IEventListener
{
    /**
     * @param TestClassEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        Assert::assertEquals('test1', $e->getEventName());
        Assert::assertEquals(TestClass::class, \get_class($e->getTarget()));
        Assert::assertEquals('imi', $e->name);
        $e->return = 19260817;
    }
}
