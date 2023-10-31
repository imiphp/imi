<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

#[Cron(id: 'CronAllWorker', second: '3n', type: \Imi\Swoole\Cron\Consts\CronTaskType::ALL_WORKER, successLog: false)]
class CronAllWorker implements ICronTask
{
    /**
     * {@inheritDoc}
     */
    public function run(string $id, $data): void
    {
        var_dump('all');
    }
}
