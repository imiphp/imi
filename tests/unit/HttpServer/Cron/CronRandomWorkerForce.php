<?php

namespace Imi\Test\HttpServer\Cron;

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
     * @param string $id
     * @param mixed  $data
     *
     * @return void
     */
    public function run(string $id, $data)
    {
        var_dump('CronRandomWorkerForce');
    }
}
