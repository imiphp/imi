<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Listener;

use Imi\Queue\Event\Param\ConsumerAfterConsumeParam;

/**
 * 消费者消费消息后置事件.
 */
interface IConsumerAfterConsumeListener
{
    /**
     * 事件处理方法.
     *
     * @return void
     */
    public function handle(ConsumerAfterConsumeParam $e);
}
