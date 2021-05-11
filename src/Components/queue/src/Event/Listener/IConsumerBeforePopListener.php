<?php

namespace Imi\Queue\Event\Listener;

use Imi\Queue\Event\Param\ConsumerBeforePopParam;

/**
 * 消费者弹出消息前置事件.
 */
interface IConsumerBeforePopListener
{
    /**
     * 事件处理方法.
     *
     * @param ConsumerBeforePopParam $e
     *
     * @return void
     */
    public function handle(ConsumerBeforePopParam $e);
}
