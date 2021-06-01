<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Listener;

use Imi\Queue\Event\Param\ConsumerAfterPopParam;

/**
 * 消费者弹出消息后置事件.
 */
interface IConsumerAfterPopListener
{
    /**
     * 事件处理方法.
     *
     * @return void
     */
    public function handle(ConsumerAfterPopParam $e);
}
