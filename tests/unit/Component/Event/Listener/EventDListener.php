<?php

declare(strict_types=1);

namespace Imi\Test\Component\Event\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Test\Component\Tests\EventTest;
use PHPUnit\Framework\Assert;

/**
 * @Listener("IMITEST.EVENT.D")
 */
class EventDListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        Assert::assertEquals('IMITEST.EVENT.D', $e->getEventName());
        Assert::assertEquals(EventTest::class, \get_class($e->getTarget()));
        $data = $e->getData();
        Assert::assertEquals('imi', $data['name']);
        $data['return'] = 19260817;
    }
}
