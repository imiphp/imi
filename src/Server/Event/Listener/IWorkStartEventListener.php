<?php
namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\WorkStartEventParam;

/**
 * 监听服务器workstart事件接口
 */
interface IWorkStartEventListener
{
	/**
	 * 事件处理方法
	 * @param WorkStartEventParam $e
	 * @return void
	 */
	public function handle(WorkStartEventParam $e);
}