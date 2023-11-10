<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Contract\ICronTask;

class CronDWorker implements ICronTask
{
    /**
     * {@inheritDoc}
     */
    public function run(string $id, mixed $data): void
    {
        var_dump('动态');
    }
}
