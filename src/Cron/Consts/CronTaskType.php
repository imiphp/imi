<?php
namespace Imi\Cron\Consts;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

/**
 * 定时任务类型
 */
abstract class CronTaskType extends BaseEnum
{
    /**
     * @EnumItem("随机工作进程任务")
     */
    const RANDOM_WORKER = 'random_worker';

    /**
     * @EnumItem("所有工作进程执行的任务")
     */
    const ALL_WORKER = 'all_worker';

    /**
     * @EnumItem("后台任务")
     */
    const TASK = 'task';

    /**
     * @EnumItem("进程")
     */
    const PROCESS = 'process';

}
