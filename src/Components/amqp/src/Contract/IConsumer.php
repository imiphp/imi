<?php

declare(strict_types=1);

namespace Imi\AMQP\Contract;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;

/**
 * 消费者.
 */
interface IConsumer
{
    /**
     * 运行消费循环.
     */
    public function run(): void;

    /**
     * 停止消费循环.
     */
    public function stop(): void;

    /**
     * 关闭.
     */
    public function close(): void;

    /**
     * Get 连接.
     */
    public function getAMQPConnection(): AbstractConnection;

    /**
     * Get 频道.
     */
    public function getAMQPChannel(): AMQPChannel;
}
