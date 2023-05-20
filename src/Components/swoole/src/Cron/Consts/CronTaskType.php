<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron\Consts;

use Imi\Enum\Annotation\EnumItem;

/**
 * 定时任务类型.
 */
class CronTaskType extends \Imi\Cron\Consts\CronTaskType
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * @EnumItem("随机工作进程任务")
     */
    public const RANDOM_WORKER = 'random_worker';

    /**
     * @EnumItem("所有工作进程执行的任务")
     */
    public const ALL_WORKER = 'all_worker';

    /**
     * @EnumItem("后台任务")
     */
    public const TASK = 'task';

    /**
     * @EnumItem("进程")
     */
    public const PROCESS = 'process';

    /**
     * @EnumItem("定时任务进程")
     */
    public const CRON_PROCESS = 'cron_process';
}
