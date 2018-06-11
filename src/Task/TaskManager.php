<?php
namespace Imi\Task;

use Imi\Task\Interfaces\ITaskParam;
use Imi\Task\Interfaces\ITaskHandler;
use Imi\ServerManage;
use Imi\Util\Call;

abstract class TaskManager
{
	/**
	 * 投递异步任务
	 * 调用成功返回任务ID，失败返回false
	 * @param TaskInfo $taskInfo
	 * @param int $workerID
	 * @return int|bool
	 */
	public static function post(TaskInfo $taskInfo, $workerID = null)
	{
		return ServerManage::getServer('main')->getSwooleServer()->task($taskInfo, $workerID, [$taskInfo->getTaskHandler(), 'finish']);
	}

	/**
	 * 投递任务，阻塞等待，单位：秒
	 * 返回值为任务直接结果
	 * @param TaskInfo $taskInfo
	 * @param float $timeout
	 * @param int $workerID
	 * @return string|bool
	 */
	public static function postWait(TaskInfo $taskInfo, $timeout, $workerID = null)
	{
		$server = ServerManage::getServer('main')->getSwooleServer();
		$result = $server->taskwait($taskInfo, $timeout, $workerID);
		Call::callUserFunc([$taskInfo->getTaskHandler(), 'finish'], $server, -1, $result);
		return $result;
	}

	/**
	 * 投递任务，协程方式等待全部执行完毕或超时，单位：秒
	 * 返回值为任务直接结果
	 * @param TaskInfo[] $tasks
	 * @param float $timeout
	 * @return array
	 */
	public static function postCo(array $tasks, $timeout)
	{
		$server = ServerManage::getServer('main')->getSwooleServer();
		$result = $server->taskCo($tasks, $timeout);
		foreach($result as $i => $item)
		{
			Call::callUserFunc([$tasks[$i]->getTaskHandler(), 'finish'], $server, -1, $item);
		}
		return $result;
	}
}