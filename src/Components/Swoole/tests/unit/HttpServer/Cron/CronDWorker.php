<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Contract\ICronTask;

class CronDWorker implements ICronTask
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
        var_dump('动态');
    }
}
