<?php
namespace Imi\Test\Component\Event\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use PHPUnit\Framework\Assert;
use Imi\Bean\Annotation\Listener;
use Imi\Test\Component\Tests\EventTest;

/**
 * @Listener("IMITEST.EVENT.D")
 */
class EventDListener implements IEventListener
{
    /**
     * 事件处理方法
     * @param \Imi\Event\EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        Assert::assertEquals('IMITEST.EVENT.D', $e->getEventName());
        Assert::assertEquals(EventTest::class, get_class($e->getTarget()));
        $data = $e->getData();
        Assert::assertEquals('imi', $data['name']);
        $data['return'] = 19260817;
    }

}