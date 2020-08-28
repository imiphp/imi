<?php
namespace Imi\Cron\Task;

use Imi\App;
use Imi\Task\TaskParam;
use Imi\Cron\Util\CronUtil;
use Imi\Task\Annotation\Task;
use Imi\Task\Interfaces\ITaskHandler;

/**
 * @Task("imiCronTask")
 */
class CronTask implements ITaskHandler
{
    /**
     * 任务处理方法，返回的值会通过 finish 事件推送给 worker 进程
     * @param TaskParam $param
     * @param \Swoole\Server $server
     * @param integer $taskId
     * @param integer $workerId
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId)
    {
        $success = false;
        $message = '';
        try {
            $paramData = $param->getData();
            $id = $paramData['id'] ?? null;
            $data = $paramData['data'] ?? null;
            $class = $paramData['class'] ?? null;
            /** @var \Imi\Cron\ICronTask $handler */
            $handler = App::getBean($class);
            $handler->run($id, $data);
            $success = true;
        } catch(\Throwable $th) {
            $message = $th->getMessage();
            throw $th;
        } finally {
            CronUtil::reportCronResult($id, $success, $message);
        }
    }

    /**
     * 任务结束时触发
     * @param \Swoole\Server $server
     * @param int $taskId
     * @param mixed $data
     * @return void
     */
    public function finish(\Swoole\Server $server, int $taskId, $data)
    {
    }

}
