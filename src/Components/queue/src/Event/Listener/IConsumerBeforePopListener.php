<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Listener;

use Imi\Queue\Event\Param\ConsumerBeforePopParam;

/**
 * 消费者弹出消息前置事件.
 */
interface IConsumerBeforePopListener
{
    public function handle(ConsumerBeforePopParam $e): void;
}
