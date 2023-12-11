<?php

declare(strict_types=1);

namespace Imi\Test\Component\Event\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Test\Component\Event\Classes\TestClassEvent;
use Imi\Test\Component\Tests\EventTest;
use PHPUnit\Framework\Assert;

#[Listener(eventName: 'IMITEST.EVENT.D')]
class EventDListener implements IEventListener
{
    /**
     * @param TestClassEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        Assert::assertEquals('IMITEST.EVENT.D', $e->getEventName());
        Assert::assertEquals(EventTest::class, \get_class($e->getTarget()));
        Assert::assertEquals('imi', $e->name);
        $e->return = 19260817;
    }
}
