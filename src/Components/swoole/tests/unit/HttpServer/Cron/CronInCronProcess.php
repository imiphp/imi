<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

#[Cron(id: 'CronInCronProcess', second: '3n', type: \Imi\Swoole\Cron\Consts\CronTaskType::CRON_PROCESS)]
class CronInCronProcess implements ICronTask
{
    /**
     * {@inheritDoc}
     */
    public function run(string $id, $data): void
    {
        var_dump('cron_process');
    }
}
