<?php
namespace Imi\Task\Interfaces;

use Imi\Task\TaskParam;

interface ITaskHandler
{
	/**
	 * 任务处理方法
	 * @param TaskParam $param
	 * @param \Swoole\Server $server
	 * @param integer $taskID
	 * @param integer $WorkerID
	 * @return void
	 */
	public function handle(TaskParam $param, \Imi\Server\Base $server, int $taskID, int $WorkerID);

	/**
	 * 任务结束时触发
	 * @param \swoole_server $server
	 * @param int $taskId
	 * @param mixed $data
	 * @return void
	 */
	public function finish(\Swoole\Server $server, int $taskID, $data);
}