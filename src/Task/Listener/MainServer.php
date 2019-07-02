<?php
namespace Imi\Task\Listener;

use Imi\Task\TaskInfo;
use Imi\RequestContext;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\TaskEventParam;
use Imi\Server\Event\Listener\ITaskEventListener;

/**
 * @Listener("IMI.MAIN_SERVER.TASK")
 */
class MainServer implements ITaskEventListener
{
    /**
     * 事件处理方法
     * @param TaskEventParam $e
     * @return void
     */
    public function handle(TaskEventParam $e)
    {
        RequestContext::create();
        try{
            $taskInfo = $e->data;
            if($taskInfo instanceof TaskInfo)
            {
                $result = $taskInfo->getTaskHandler()->handle($taskInfo->getParam(), $e->server->getSwooleServer(), $e->taskID, $e->workerID);
                if($e->task)
                {
                    $e->task->finish($result);
                }
                else
                {
                    $e->server->getSwooleServer()->finish($result);
                }
            }
        }
        catch(\Throwable $ex)
        {
            throw $ex;
        }
        finally{
            RequestContext::destroy();
        }
    }
}