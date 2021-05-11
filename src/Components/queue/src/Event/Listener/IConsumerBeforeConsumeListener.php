<?php

namespace Imi\Queue\Event\Listener;

use Imi\Queue\Event\Param\ConsumerBeforeConsumeParam;

/**
 * 消费者消费消息前置事件.
 */
interface IConsumerBeforeConsumeListener
{
    /**
     * 事件处理方法.
     *
     * @param ConsumerBeforeConsumeParam $e
     *
     * @return void
     */
    public function handle(ConsumerBeforeConsumeParam $e);
}
