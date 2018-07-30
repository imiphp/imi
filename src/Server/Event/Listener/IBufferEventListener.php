<?php
namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\BufferEventParam;

/**
 * 监听服务器bufferFull/bufferEmpty事件接口
 */
interface IBufferEventListener
{
	/**
	 * 事件处理方法
	 * @param BufferEventParam $e
	 * @return void
	 */
	public function handle(BufferEventParam $e);
}