<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Cron;

use Imi\Cron\Annotation\Cron;
use Imi\Cron\Util\CronUtil;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;

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
     * @param int            $taskId
     * @param int            $WorkerId
     *
     * @return void
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $WorkerId)
    {
        CronUtil::reportCronResult($param->getData()['id'], true, '');

        return date('Y-m-d H:i:s');
    }

    /**
     * 任务结束时触发.
     *
     * @param \swoole_server $server
     * @param int            $taskId
     * @param mixed          $data
     *
     * @return void
     */
    public function finish(\Swoole\Server $server, int $taskId, $data)
    {
    }
}
