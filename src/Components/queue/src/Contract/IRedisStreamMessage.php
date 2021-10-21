<?php

declare(strict_types=1);

namespace Imi\Queue\Contract;

/**
 * 消息接口.
 */
interface IRedisStreamMessage extends IMessage
{
    /**
     * 获取数组消息.
     */
    public function getArrayMessage(): array;
}
