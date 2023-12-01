<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Log\Log;
use Imi\Util\ImiPriority;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Server\Http\Event\WorkermanErrorEvent;

/**
 * Error事件后置处理.
 */
#[Listener(eventName: WorkermanEvents::SERVER_ERROR, priority: ImiPriority::MIN)]
class AfterError implements IEventListener
{
    /**
     * @param WorkermanErrorEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        Log::error(sprintf('Workerman error: %s, errCode: %s', $e->msg, $e->code));
    }
}
