<?php

declare(strict_types=1);

namespace Imi\Cron\Consts;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * 定时任务类型.
 */
class CronTaskType extends BaseEnum
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * @EnumItem("定时任务进程")
     */
    public const CRON_PROCESS = 'cron_process';
}
