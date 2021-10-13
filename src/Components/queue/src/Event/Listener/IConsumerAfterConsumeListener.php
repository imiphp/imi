<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Listener;

use Imi\Queue\Event\Param\ConsumerAfterConsumeParam;

/**
 * 消费者消费消息后置事件.
 */
interface IConsumerAfterConsumeListener
{
    public function handle(ConsumerAfterConsumeParam $e): void;
}
