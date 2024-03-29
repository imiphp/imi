<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

#[Cron(id: 'CronRandomWorker', second: '3n', type: \Imi\Swoole\Cron\Consts\CronTaskType::RANDOM_WORKER)]
class CronRandomWorker implements ICronTask
{
    /**
     * {@inheritDoc}
     */
    public function run(string $id, mixed $data): void
    {
        var_dump('random');
    }
}
