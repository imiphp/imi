<?php
namespace Imi\Listener;

use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Util\CoroutineChannelManager;

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
		CoroutineChannelManager::setNames($main->getCoroutineChannels());
		CoroutineChannelManager::init();
	}
}