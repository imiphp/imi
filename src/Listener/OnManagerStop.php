<?php
namespace Imi\Listener;

use Imi\Process\ProcessManager;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\ManagerStopEventParam;
use Imi\Server\Event\Listener\IManagerStopEventListener;
use Imi\App;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.MANAGER.STOP",priority=PHP_INT_MIN)
 */
class OnManagerStop implements IManagerStopEventListener
{
	/**
	 * 事件处理方法
	 * @param ManagerStopEventParam $e
	 * @return void
	 */
	public function handle(ManagerStopEventParam $e)
	{
		App::getBean('Logger')->save();
	}
}