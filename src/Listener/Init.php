<?php
namespace Imi\Listener;

use Imi\App;
use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Util\AtomicManager;
use Imi\Util\ChannelManager;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

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
		App::getBean('ErrorLog')->register();
		foreach(Helper::getMains() as $main)
		{
			$config = $main->getConfig();
			// 原子计数初始化
			AtomicManager::setNames($config['atomics'] ?? []);
			// 通道队列初始化
			ChannelManager::setNames($config['channels'] ?? []);
		}
		AtomicManager::init();
		ChannelManager::init();
	}
}