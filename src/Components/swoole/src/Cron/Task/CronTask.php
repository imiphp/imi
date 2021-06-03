<?php

declare(strict_types=1);

namespace Imi\Swoole\Cron\Task;

use Imi\App;
use Imi\Cron\Util\CronUtil;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\Interfaces\ITaskHandler;
use Imi\Swoole\Task\TaskParam;

/**
 * @Task("imiCronTask")
 */
class CronTask implements ITaskHandler
{
    /**
     * 任务处理方法，返回的值会通过 finish 事件推送给 worker 进程.
     *
     * @return mixed
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId)
    {
        $success = false;
        $message = '';
        $paramData = $param->getData();
        $id = $paramData['id'] ?? null;
        $data = $paramData['data'] ?? null;
        $class = $paramData['class'] ?? null;
        try
        {
            /** @var \Imi\Cron\Contract\ICronTask $handler */
            $handler = App::getBean($class);
            $handler->run($id, $data);
            $success = true;
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage();
            throw $th;
        }
        finally
        {
            CronUtil::reportCronResult($id, $success, $message);
        }
    }

    /**
     * 任务结束时触发.
     *
     * @param mixed $data
     */
    public function finish(\Swoole\Server $server, int $taskId, $data): void
    {
    }
}
