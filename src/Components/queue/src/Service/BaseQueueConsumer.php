<?php

declare(strict_types=1);

namespace Imi\Queue\Service;

use Imi\Aop\Annotation\Inject;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;

/**
 * 队列消费基类.
 */
abstract class BaseQueueConsumer
{
    /**
     * @Inject("imiQueue")
     *
     * @var \Imi\Queue\Service\QueueService
     */
    protected QueueService $imiQueue;

    /**
     * 队列名称.
     */
    protected string $name;

    /**
     * 是否正在工作.
     */
    protected bool $working = false;

    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    /**
     * 开始消费循环.
     */
    public function start(?int $co = null): void
    {
        throw new \RuntimeException('The method is not implemented');
    }

    /**
     * 停止消费.
     */
    public function stop(): void
    {
        throw new \RuntimeException('The method is not implemented');
    }

    /**
     * 处理消费.
     */
    abstract protected function consume(IMessage $message, IQueueDriver $queue): void;
}
