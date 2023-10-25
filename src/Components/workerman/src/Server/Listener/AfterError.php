<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;

/**
 * Error事件后置处理.
 */
#[Listener(eventName: 'IMI.WORKERMAN.SERVER.ERROR', priority: (-9223372036854775807 - 1))]
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
