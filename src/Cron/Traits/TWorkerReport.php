<?php

declare(strict_types=1);

namespace Imi\Cron\Traits;

use Imi\Cron\Util\CronUtil;

/**
 * 定时任务上报.
 */
trait TWorkerReport
{
    /**
     * 上报定时任务结果.
     */
    protected function reportCronResult(string $id, bool $success, string $message): void
    {
        CronUtil::reportCronResult($id, $success, $message);
    }
}
