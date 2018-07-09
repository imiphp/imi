<?php
namespace Imi\Listener;

use Imi\Main\Helper;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation;
use Imi\Pool\PoolConfig;
use Imi\Event\EventParam;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Util\CoroutineChannelManager;
use Imi\App;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START",priority=PHP_INT_MAX)
 */
class WorkerInit implements IEventListener
{
	/**
	 * 事件处理方法
	 * @param EventParam $e
	 * @return void
	 */
	public function handle(EventParam $e)
	{
		App::initWorker();
	}
}