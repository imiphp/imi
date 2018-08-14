<?php
namespace Imi\Listener;

use Imi\App;
use Imi\Config;
use Imi\Worker;
use Imi\Util\File;
use Imi\Main\Helper;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation;
use Imi\Pool\PoolConfig;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Bean\Annotation\Listener;
use Imi\Util\CoroutineChannelManager;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START",priority=PHP_INT_MAX)
 */
class WorkerInit implements IWorkerStartEventListener
{
	/**
	 * 事件处理方法
	 * @param EventParam $e
	 * @return void
	 */
	public function handle(WorkerStartEventParam $e)
	{
		$GLOBALS['WORKER_START_END_RESUME_COIDS'] = [];

		// 清除当前 worker 进程的 Bean 类缓存
		$path = Imi::getWorkerClassCachePathByWorkerID($e->server->getSwooleServer()->worker_id);
		foreach (File::enum($path) as $file)
		{
			if (is_file($file))
			{
				unlink($file);
			}
		}

		// 当前进程的 WorkerID 设置
		Worker::setWorkerID($e->server->getSwooleServer()->worker_id);

		// 初始化 worker
		App::initWorker();
	}
}