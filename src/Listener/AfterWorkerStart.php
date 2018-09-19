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
use Imi\Server\Event\Param\PipeMessageEventParam;
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
		$checkResult = null;
		if(0 === Worker::getWorkerID() && !($checkResult = $this->checkInitFlagFile($initFlagFile)))
		{
			Event::trigger('IMI.APP.INIT', [
				
			], $e->getTarget(), AppInitEventParam::class);

			file_put_contents($initFlagFile, Swoole::getMasterPID());

			$server = $e->server->getSwooleServer();
			// 触发当前进程的PipeMessage事件
			Event::trigger('IMI.MAIN_SERVER.PIPE_MESSAGE', [
				'server'	=>	$e->server,
				'workerID'	=>	$e->workerID,
				'message'	=>	'app.inited',
			], $this, PipeMessageEventParam::class);
			// 通知其它worker进程
			for($i = 1; $i < $server->setting['worker_num']; ++$i)
			{
				$server->sendMessage('app.inited', $i);
			}
		}
		else if($checkResult || (null === $checkResult && $this->checkInitFlagFile($initFlagFile)))
		{
			// 热重启后，触发当前进程的PipeMessage事件
			Event::trigger('IMI.MAIN_SERVER.PIPE_MESSAGE', [
				'server'	=>	$e->server,
				'workerID'	=>	$e->workerID,
				'message'	=>	'app.inited',
			], $this, PipeMessageEventParam::class);
		}
	}

	/**
	 * 检测是否当前服务已初始化
	 *
	 * @param string $initFlagFile
	 * @return boolean
	 */
	private function checkInitFlagFile($initFlagFile)
	{
		return is_file($initFlagFile) && file_get_contents($initFlagFile) == Swoole::getMasterPID();
	}
}