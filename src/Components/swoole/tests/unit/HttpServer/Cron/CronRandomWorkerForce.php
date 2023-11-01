<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

#[Cron(id: 'CronRandomWorkerForce', minute: '1n', second: '0', type: \Imi\Swoole\Cron\Consts\CronTaskType::RANDOM_WORKER, force: true)]
class CronRandomWorkerForce implements ICronTask
{
    /**
     * {@inheritDoc}
     */
    public function run(string $id, $data): void
    {
        var_dump('CronRandomWorkerForce');
    }
}
