<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Event\QueueEvents;

/**
 * 消费者消费消息后置事件参数.
 */
class ConsumerAfterConsumeParam extends CommonEvent
{
    public function __construct(
        /**
         * 队列对象
         */
        public readonly ?IQueueDriver $queue = null,
        /**
         * 消息.
         */
        public readonly ?IMessage $message = null)
    {
        parent::__construct(QueueEvents::AFTER_CONSUME);
    }
}
