<?php
namespace Imi\Listener;

use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Util\AtomicManager;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Util\ChannelManager;

/**
 * @Listener(eventName="IMI.INITED",priority=PHP_INT_MAX)
 */
class Init implements IEventListener
{
	/**
	 * 事件处理方法
	 * @param EventParam $e
	 * @return void
	 */
	public function handle(EventParam $e)
	{
		foreach(Helper::getMains() as $main)
		{
			// 原子计数初始化
			AtomicManager::setNames($main->getConfig()['atomics'] ?? []);
			// 通道队列初始化
			ChannelManager::setNames($main->getConfig()['channels'] ?? []);
		}
		AtomicManager::init();
		ChannelManager::init();
	}
}