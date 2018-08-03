<?php
namespace Imi\Listener;

use Imi\Config;
use Imi\Util\File;
use Imi\Bean\Annotation;
use Imi\Event\EventParam;
use Imi\Bean\Annotation\Listener;
use Imi\Main\Helper as MainHelper;
use Imi\Server\Event\Param\StartEventParam;
use Imi\Server\Event\Listener\IStartEventListener;
use Imi\Server\Event\Param\ManagerStartEventParam;
use Imi\Server\Event\Listener\IManagerStartEventListener;
use Imi\Process\ProcessManager;
use Imi\Util\Swoole;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.MANAGER.START",priority=PHP_INT_MAX)
 */
class OnManagerStart implements IManagerStartEventListener
{
	/**
	 * 事件处理方法
	 * @param ManagerStartEventParam $e
	 * @return void
	 */
	public function handle(ManagerStartEventParam $e)
	{
		// 进程PID记录
		$fileName = File::path(dirname($_SERVER['SCRIPT_NAME']), 'imi.pid');
		File::writeFile($fileName, json_encode([
			'masterPID'		=>	Swoole::getMasterPID(),
			'managerPID'	=>	Swoole::getManagerPID(),
		]));
		
		// 热更新
		$process = ProcessManager::create('hotUpdate');
		$process->start();
	}
}