<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron\Consts;

/**
 * 定时任务类型.
 */
class CronTaskType
{
    use \Imi\Util\Traits\TStaticClass;

    public const RANDOM_WORKER = 'random_worker';

    public const ALL_WORKER = 'all_worker';

    public const TASK = 'task';

    public const PROCESS = 'process';

    public const CRON_PROCESS = 'cron_process';
}
