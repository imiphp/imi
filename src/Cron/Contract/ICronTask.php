<?php

declare(strict_types=1);

namespace Imi\Cron\Contract;

/**
 * 定时任务接口.
 */
interface ICronTask
{
    /**
     * 执行任务
     */
    public function run(string $id, mixed $data): void;
}
