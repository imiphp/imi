<?php
namespace Imi\Listener;

use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Util\AtomicManager;
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
		// 原子计数初始化
		foreach(Helper::getMains() as $main)
		{
			AtomicManager::setNames($main->getAtomics());
		}
		AtomicManager::init();
	}
}