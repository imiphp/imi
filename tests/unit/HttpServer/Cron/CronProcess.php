<?php
namespace Imi\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Contract\ICronTask;

/**
 * @Cron(id="CronProcess1", second="3n", type="process")
 */
class CronProcess implements ICronTask
{
    /**
     * 执行任务
     *
     * @param string $id
     * @param mixed $data
     * @return void
     */
    public function run(string $id, $data)
    {
        var_dump('process:' . $id);
    }

}
