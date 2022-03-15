<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;
use Imi\Util\ImiPriority;

/**
 * Error事件后置处理.
 *
 * @Listener(eventName="IMI.WORKERMAN.SERVER.ERROR", priority=ImiPriority::MIN)
 */
class AfterError implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        Log::error(sprintf('Workerman error: %s, errCode: %s', $data['msg'], $data['code']));
    }
}
