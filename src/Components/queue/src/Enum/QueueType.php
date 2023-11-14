<?php

declare(strict_types=1);

namespace Imi\Queue\Enum;

/**
 * 队列类型.
 */
enum QueueType: int implements IQueueType
{
    /**
     * 准备就绪.
     */
    case Ready = 1;

    /**
     * 工作中.
     */
    case Working = 2;

    /**
     * 失败.
     */
    case Fail = 3;

    /**
     * 超时.
     */
    case Timeout = 4;

    /**
     * 延时.
     */
    case Delay = 5;

    public function structType(): string
    {
        return match ($this)
        {
            self::Ready, self::Fail, self::Timeout => 'list',
            self::Working, self::Delay => 'zset',
        };
    }
}
