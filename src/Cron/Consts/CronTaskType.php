<?php

declare(strict_types=1);

namespace Imi\Cron\Consts;

/**
 * 定时任务类型.
 */
class CronTaskType
{
    use \Imi\Util\Traits\TStaticClass;

    public const CRON_PROCESS = 'cron_process';
}
