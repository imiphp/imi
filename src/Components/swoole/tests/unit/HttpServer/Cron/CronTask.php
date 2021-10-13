<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

/**
 * @Cron(id="CronTask", second="3n", type="task")
 */
class CronTask implements ICronTask
{
    /**
     * {@inheritDoc}
     */
    public function run(string $id, $data): void
    {
        var_dump('task');
    }
}
