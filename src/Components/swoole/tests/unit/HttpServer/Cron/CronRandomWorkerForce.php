<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

/**
 * @Cron(id="CronRandomWorkerForce", minute="1n", second="0", type="random_worker", force=true)
 */
class CronRandomWorkerForce implements ICronTask
{
    /**
     * 执行任务
     *
     * @param mixed $data
     */
    public function run(string $id, $data): void
    {
        var_dump('CronRandomWorkerForce');
    }
}
