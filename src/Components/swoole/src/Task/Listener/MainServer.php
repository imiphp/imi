<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\ITaskEventListener;
use Imi\Swoole\Server\Event\Param\TaskEventParam;
use Imi\Swoole\Task\TaskInfo;

/**
 * @Listener("IMI.MAIN_SERVER.TASK")
 */
class MainServer implements ITaskEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(TaskEventParam $e): void
    {
        $taskInfo = $e->data;
        if ($taskInfo instanceof TaskInfo)
        {
            $workerId = $e->workerId;
            $swooleServer = $e->server->getSwooleServer();
            $result = $taskInfo->getTaskHandler()->handle($taskInfo->getParam(), $swooleServer, $e->taskId, $workerId);
            if ($workerId >= 0 && $workerId < $swooleServer->setting['worker_num'])
            {
                if ($e->task)
                {
                    $e->task->finish($result);
                }
                else
                {
                    $swooleServer->finish($result);
                }
            }
        }
    }
}
