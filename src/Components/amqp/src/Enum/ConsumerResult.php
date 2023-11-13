<?php

declare(strict_types=1);

namespace Imi\AMQP\Enum;

/**
 * 消费者执行结果.
 */
enum ConsumerResult: int
{
    /**
     * 确认消息.
     */
    case Ack = 1;

    /**
     * 否定消息.
     */
    case Nack = 2;

    /**
     * 否定消息，并重回队列.
     */
    case NackRequeue = 3;

    /**
     * 拒绝消息.
     */
    case Reject = 4;

    /**
     * 拒绝消息，并重回队列.
     */
    case RejectRequeue = 5;
}
