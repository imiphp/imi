<?php
namespace Imi\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Worker;
use Imi\Util\Coroutine;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START",priority=PHP_INT_MIN)
 */
class AfterWorkerStart implements IWorkerStartEventListener
{
	/**
	 * 事件处理方法
	 * @param EventParam $e
	 * @return void
	 */
	public function handle(WorkerStartEventParam $e)
	{
		Worker::inited();
		foreach($GLOBALS['WORKER_START_END_RESUME_COIDS'] as $id)
		{
			Coroutine::resume($id);
		}
		unset($GLOBALS['WORKER_START_END_RESUME_COIDS']);
	}
}