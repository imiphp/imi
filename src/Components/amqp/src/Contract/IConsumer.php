<?php

declare(strict_types=1);

namespace Imi\AMQP\Contract;

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
}
