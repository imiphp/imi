<?php

namespace Imi\Cron\Contract;

/**
 * 定时任务接口.
 */
interface ICronTask
{
    /**
     * 执行任务
     *
     * @param string $id
     * @param mixed  $data
     *
     * @return void
     */
    public function run(string $id, $data);
}
