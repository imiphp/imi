<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Contract\ICronTask;

class CronDWorker implements ICronTask
{
    /**
     * 执行任务
     *
     * @param mixed $data
     */
    public function run(string $id, $data): void
    {
        var_dump('动态');
    }
}
