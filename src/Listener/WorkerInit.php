<?php
namespace Imi\Listener;

use Imi\App;
use Imi\Config;
use Imi\Main\Helper;
use Imi\RequestContext;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation;
use Imi\Pool\PoolConfig;
use Imi\Event\EventParam;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Util\CoroutineChannelManager;
use Imi\Server\Route\Annotation\Controller;
use Imi\Server\Route\Parser\ControllerParser;

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
		// 加载服务器注解
		Annotation::getInstance()->init(Helper::getAppMains());
		
		// 初始化
		$main = Helper::getMain($e->getTarget()->getConfig()['namespace']);
		if(Coroutine::isIn())
		{
			// 协程相关的初始化操作

			// 协程通道队列初始化
			CoroutineChannelManager::setNames($main->getConfig()['coroutineChannels'] ?? []);

			// 异步池子初始化
			$pools = array_merge(Helper::getMain(App::getNamespace())->getConfig()['pools'] ?? [], $main->getConfig()['pools'] ?? []);
			foreach($pools as $name => $pool)
			{
				if(isset($pool['async']))
				{
					$pool = $pool['async'];
					PoolManager::addName($name, $pool['pool']['class'], new PoolConfig($pool['pool']['config']), $pool['resource']);
				}
			}
		}
		else
		{
			// 同步池子初始化
			$pools = array_merge(Helper::getMain(App::getNamespace())->getConfig()['pools'] ?? [], $main->getConfig()['pools'] ?? []);
			foreach($pools as $name => $pool)
			{
				if(isset($pool['sync']))
				{
					$pool = $pool['sync'];
					PoolManager::addName($name, $pool['pool']['class'], new PoolConfig($pool['pool']['config']), $pool['resource']);
				}
			}
		}
		// 缓存初始化
		$caches = array_merge(Helper::getMain(App::getNamespace())->getConfig()['caches'] ?? [], $main->getConfig()['caches'] ?? []);
		foreach($caches as $name => $cache)
		{
			CacheManager::addName($name, $cache['handlerClass'], $cache['option']);
		}
	}
}