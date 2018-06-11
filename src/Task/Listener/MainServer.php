<?php
namespace Imi\Task\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\TaskEventParam;
use Imi\Server\Event\Listener\ITaskEventListener;
use Imi\Task\TaskInfo;
use Imi\Util\Call;
use Imi\RequestContext;

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
				Call::callUserFunc([$taskInfo->getTaskHandler(), 'handle'], $taskInfo->getParam(), $e->server, $e->taskID, $e->workerID);
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