<?php

namespace Imi\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Util\CronUtil;
use Imi\Task\Annotation\Task;
use Imi\Task\Interfaces\ITaskHandler;
use Imi\Task\TaskParam;

/**
 * @Cron(id="TaskCron", second="3n", data={"id":"TaskCron"})
 * @Task("CronTask1")
 */
class TaskCron implements ITaskHandler
{
    /**
     * 任务处理方法.
     *
     * @param TaskParam      $param
     * @param \Swoole\Server $server
     * @param int            $taskID
     * @param int            $workerID
     *
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskID, int $workerID)
    {
        CronUtil::reportCronResult($param->getData()['id'], true, '');

        return date('Y-m-d H:i:s');
    }

    /**
     * 任务结束时触发.
     *
     * @param \swoole_server $server
     * @param int            $taskID
     * @param mixed          $data
     *
     * @return void
     */
    public function finish(\Swoole\Server $server, int $taskID, $data)
    {
    }
}
