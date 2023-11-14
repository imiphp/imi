<?php

declare(strict_types=1);

namespace Imi\Cron\Consts;

/**
 * 任务唯一性等级.
 */
enum UniqueLevel
{
    /**
     * 当前实例唯一
     */
    case Current = 'current';

    /**
     * 所有实例唯一
     */
    case All = 'all';
}
