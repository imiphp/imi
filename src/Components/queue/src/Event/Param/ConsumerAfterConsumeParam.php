<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Param;

use Imi\Event\EventParam;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;

/**
 * 消费者消费消息后置事件参数.
 */
class ConsumerAfterConsumeParam extends EventParam
{
    /**
     * 队列对象
     */
    public ?IQueueDriver $queue = null;

    /**
     * 消息.
     */
    public ?IMessage $message = null;
}
