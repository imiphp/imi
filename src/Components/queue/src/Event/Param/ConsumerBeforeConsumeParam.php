<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Param;

use Imi\Event\EventParam;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;

/**
 * 消费者消费消息前置事件参数.
 */
class ConsumerBeforeConsumeParam extends EventParam
{
    /**
     * 队列对象
     */
    public IQueueDriver $queue;

    /**
     * 消息.
     */
    public IMessage $message;
}
