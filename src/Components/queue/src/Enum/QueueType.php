<?php

declare(strict_types=1);

namespace Imi\Queue\Enum;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;
use Imi\Queue\Annotation\QueueTypeStructType;

/**
 * 队列类型.
 */
class QueueType extends BaseEnum
{
    use \Imi\Util\Traits\TStaticClass;

    #[
        EnumItem(text: '准备就绪'),
        QueueTypeStructType(type: 'list')
    ]
    public const READY = 1;

    #[
        EnumItem(text: '工作中'),
        QueueTypeStructType(type: 'zset')
    ]
    public const WORKING = 2;

    #[
        EnumItem(text: '失败'),
        QueueTypeStructType(type: 'list')
    ]
    public const FAIL = 3;

    #[
        EnumItem(text: '超时'),
        QueueTypeStructType(type: 'list')
    ]
    public const TIMEOUT = 4;

    #[
        EnumItem(text: '延时'),
        QueueTypeStructType(type: 'zset')
    ]
    public const DELAY = 5;
}
