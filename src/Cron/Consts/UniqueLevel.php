<?php

declare(strict_types=1);

namespace Imi\Cron\Consts;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * 任务唯一性等级.
 */
class UniqueLevel extends BaseEnum
{
    use \Imi\Util\Traits\TStaticClass;

    #[EnumItem(text: '当前实例唯一')]
    public const CURRENT = 'current';

    #[EnumItem(text: '所有实例唯一')]
    public const ALL = 'all';
}
