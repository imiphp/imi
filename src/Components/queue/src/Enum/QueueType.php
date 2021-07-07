<?php

declare(strict_types=1);

namespace Imi\Queue\Enum;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * 队列类型.
 */
abstract class QueueType extends BaseEnum
{
    /**
     * @EnumItem(text="准备就绪", type="list")
     */
    public const READY = 1;

    /**
     * @EnumItem(text="工作中", type="zset")
     */
    public const WORKING = 2;

    /**
     * @EnumItem(text="失败", type="list")
     */
    public const FAIL = 3;

    /**
     * @EnumItem(text="超时", type="list")
     */
    public const TIMEOUT = 4;

    /**
     * @EnumItem(text="延时", type="zset")
     */
    public const DELAY = 5;
}
