<?php

namespace Imi\Queue\Event\Param;

use Imi\Event\EventParam;

/**
 * 消费者弹出消息前置事件参数.
 */
class ConsumerBeforePopParam extends EventParam
{
    /**
     * 队列对象
     *
     * @var \Imi\Queue\Driver\IQueueDriver
     */
    public $queue;
}
