<?php
namespace Imi\Test\HttpServer\Cron;

use Imi\Task\TaskParam;
use Imi\Cron\Util\CronUtil;
use Imi\Cron\Annotation\Cron;
use Imi\Task\Annotation\Task;
use Imi\Task\Interfaces\ITaskHandler;

/**
 * @Cron(id="TaskCron", second="3n", data={"id":"TaskCron"})
 * @Task("CronTask1")
 */
class TaskCron implements ITaskHandler
{
    /**
     * 任务处理方法
     * @param TaskParam $param
     * @param \Swoole\Server $server
     * @param integer $taskID
     * @param integer $WorkerID
     * @return void
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskID, int $WorkerID)
    {
        CronUtil::reportCronResult($param->getData()['id'], true, '');
        return date('Y-m-d H:i:s');
    }
 
    /**
     * 任务结束时触发
     * @param \swoole_server $server
     * @param int $taskId
     * @param mixed $data
     * @return void
     */
    public function finish(\Swoole\Server $server, int $taskID, $data)
    {
    }

}