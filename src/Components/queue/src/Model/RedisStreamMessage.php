<?php

declare(strict_types=1);

namespace Imi\Queue\Model;

use Imi\Queue\Contract\IRedisStreamMessage;

/**
 * 消息.
 */
class RedisStreamMessage extends Message implements IRedisStreamMessage
{
    /**
     * 获取数组消息.
     */
    public function getArrayMessage(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
