<?php

namespace Imi\Cron\Consts;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * 任务唯一性等级.
 */
abstract class UniqueLevel extends BaseEnum
{
    /**
     * @EnumItem("当前实例唯一")
     */
    const CURRENT = 'current';

    /**
     * @EnumItem("所有实例唯一")
     */
    const ALL = 'all';
}
