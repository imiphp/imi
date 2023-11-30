<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Queue\Driver\IQueueDriver;

/**
 * 消费者弹出消息前置事件参数.
 */
class ConsumerBeforePopParam extends CommonEvent
{
    public function __construct(
        /**
         * 队列对象
         */
        public readonly ?IQueueDriver $queue = null)
    {
        parent::__construct('IMI.QUEUE.CONSUMER.BEFORE_POP');
    }
}
