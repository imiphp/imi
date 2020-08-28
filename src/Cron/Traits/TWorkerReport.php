<?php
namespace Imi\Cron\Traits;

use Imi\Cron\Util\CronUtil;

/**
 * 定时任务上报
 */
trait TWorkerReport
{
    /**
     * 上报定时任务结果
     *
     * @param string $id
     * @param bool $success
     * @param string $message
     * @return void
     */
    protected function reportCronResult(string $id, bool $success, string $message)
    {
        CronUtil::reportCronResult($id, $success, $message);
    }

}
