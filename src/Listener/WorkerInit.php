<?php
namespace Imi\Listener;

use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Util\CoroutineChannelManager;
use Imi\App;
use Imi\Cache\CacheManager;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORK.START",priority=PHP_INT_MAX)
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
		$main = Helper::getMain($e->getTarget()->getConfig()['namespace']);
		// 协程通道队列初始化
		CoroutineChannelManager::setNames($main->getConfig()['coroutineChannels'] ?? []);

		// 池子初始化
		$pools = array_merge(Helper::getMain(App::getNamespace())->getConfig()['pools'] ?? [], $main->getConfig()['pools'] ?? []);
		foreach($pools as $name => $pool)
		{
			\Imi\Pool\PoolManager::addName($name, $pool['pool']['class'], new \Imi\Pool\PoolConfig($pool['pool']['config']), $pool['resource']);
		}

		// 缓存初始化
		$caches = array_merge(Helper::getMain(App::getNamespace())->getConfig()['caches'] ?? [], $main->getConfig()['caches'] ?? []);
		foreach($caches as $name => $cache)
		{
			CacheManager::addName($name, $cache['handlerClass'], $cache['option']);
		}
	}
}