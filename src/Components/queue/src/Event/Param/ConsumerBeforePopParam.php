<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Param;

use Imi\Event\EventParam;
use Imi\Queue\Driver\IQueueDriver;

/**
 * 消费者弹出消息前置事件参数.
 */
class ConsumerBeforePopParam extends EventParam
{
    /**
     * 队列对象
     */
    public IQueueDriver $queue;
}
