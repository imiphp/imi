<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

#[Cron(id: 'CronProcess1', second: '3n', type: \Imi\Swoole\Cron\Consts\CronTaskType::PROCESS)]
class CronProcess implements ICronTask
{
    /**
     * {@inheritDoc}
     */
    public function run(string $id, mixed $data): void
    {
        var_dump('process:' . $id);
    }
}
