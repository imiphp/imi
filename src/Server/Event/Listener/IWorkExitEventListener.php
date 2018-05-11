<?php
namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\WorkExitEventParam;

/**
 * 监听服务器workexit事件接口
 */
interface IWorkExitEventListener
{
	/**
	 * 事件处理方法
	 * @param WorkExitEventParam $e
	 * @return void
	 */
	public function handle(WorkExitEventParam $e);
}