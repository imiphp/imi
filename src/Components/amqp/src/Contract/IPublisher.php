<?php

declare(strict_types=1);

namespace Imi\AMQP\Contract;

/**
 * 发布者.
 */
interface IPublisher
{
    /**
     * 发布消息.
     */
    public function publish(IMessage $message): bool;
}
