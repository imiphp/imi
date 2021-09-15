<?php

declare(strict_types=1);

namespace Imi\AMQP\Contract;

use Imi\AMQP\Message;

interface IQueueConsumer extends IConsumer
{
    /**
     * 重新打开
     */
    public function reopen(): void;

    /**
     * 弹出消息.
     */
    public function pop(float $timeout): ?Message;
}
