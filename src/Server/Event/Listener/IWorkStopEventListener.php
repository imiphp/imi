<?php
namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\WorkStopEventParam;

/**
 * 监听服务器workstop事件接口
 */
interface IWorkStopEventListener
{
	/**
	 * 事件处理方法
	 * @param WorkStopEventParam $e
	 * @return void
	 */
	public function handle(WorkStopEventParam $e);
}