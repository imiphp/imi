<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Listener;

use Imi\Queue\Event\Param\ConsumerAfterPopParam;

/**
 * 消费者弹出消息后置事件.
 */
interface IConsumerAfterPopListener
{
    public function handle(ConsumerAfterPopParam $e): void;
}
