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
     * {@inheritDoc}
     */
    public function getArrayMessage(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
