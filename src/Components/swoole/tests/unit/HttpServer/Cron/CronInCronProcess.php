<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

/**
 * @Cron(id="CronInCronProcess", second="3n", type="cron_process")
 */
class CronInCronProcess implements ICronTask
{
    /**
     * 执行任务
     *
     * @param mixed $data
     */
    public function run(string $id, $data): void
    {
        var_dump('cron_process');
    }
}
