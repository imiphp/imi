<?php
namespace Imi\Task\Listener;

use Imi\Event\EventParam;
use Imi\Server\Event\Param\TaskCoEventParam;
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
     * @param EventParam $e
     * @return void
     */
    public function handle($e)
    {
        RequestContext::create();
        try{
            if($e instanceof TaskCoEventParam){
                /**
                 * @var $e TaskCoEventParam
                 */
                $taskInfo = $e->task->data;
                if($taskInfo instanceof TaskInfo)
                {
                    call_user_func([$taskInfo->getTaskHandler(), 'handle'], $taskInfo->getParam(), $e->server->getSwooleServer(), $e->task->id, $e->task->worker_id);
                }
            }else{
                /**
                 * @var $e TaskEventParam
                 */
                $taskInfo = $e->data;
                if($taskInfo instanceof TaskInfo)
                {
                    call_user_func([$taskInfo->getTaskHandler(), 'handle'], $taskInfo->getParam(), $e->server->getSwooleServer(), $e->taskID, $e->workerID);
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