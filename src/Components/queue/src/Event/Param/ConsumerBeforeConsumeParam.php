<?php

declare(strict_types=1);

namespace Imi\Queue\Event\Param;

use Imi\Event\EventParam;

/**
 * 消费者消费消息前置事件参数.
 */
class ConsumerBeforeConsumeParam extends EventParam
{
    /**
     * 队列对象
     *
     * @var \Imi\Queue\Driver\IQueueDriver
     */
    public $queue;

    /**
     * 消息.
     *
     * @var \Imi\Queue\Contract\IMessage
     */
    public $message;
}
