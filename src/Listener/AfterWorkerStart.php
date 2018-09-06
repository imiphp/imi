<?php
namespace Imi\Listener;

use Imi\App;
use Imi\Worker;
use Imi\Util\File;
use Imi\Event\Event;
use Imi\Util\Swoole;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\AppInitEventParam;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IWorkerStartEventListener;

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
		// 项目初始化事件
		$initFlagFile = File::path(dirname($_SERVER['SCRIPT_NAME']), str_replace('\\', '-', App::getNamespace()) . '.app.init');
		if(0 === Worker::getWorkerID() && (!is_file($initFlagFile) || file_get_contents($initFlagFile) != Swoole::getMasterPID()))
		{
			Event::trigger('IMI.APP.INIT', [
				
			], $e->getTarget(), AppInitEventParam::class);

			file_put_contents($initFlagFile, Swoole::getMasterPID());
		}
		else
		{
			while(true)
			{
				if(is_file($initFlagFile) && file_get_contents($initFlagFile) == Swoole::getMasterPID())
				{
					break;
				}
				sleep(1);
			}
		}
		// worker 初始化
		Worker::inited();
		foreach($GLOBALS['WORKER_START_END_RESUME_COIDS'] as $id)
		{
			Coroutine::resume($id);
		}
		unset($GLOBALS['WORKER_START_END_RESUME_COIDS']);

		// 触发项目的workerstart事件
		Event::trigger('IMI.MAIN_SERVER.WORKER.START.APP', [
			'server'	=>	$e->server,
			'workerID'	=>	$e->workerID,
		], $e->getTarget(), WorkerStartEventParam::class);
	}
}