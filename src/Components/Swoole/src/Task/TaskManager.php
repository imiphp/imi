<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\ServerManage;
use Imi\Swoole\Task\Handler\BeanTaskHandler;
use Imi\Swoole\Task\Parser\TaskParser;

class TaskManager
{
    private function __construct()
    {
    }

    /**
     * 投递异步任务
     * 调用成功返回任务ID，失败返回false.
     *
     * @param TaskInfo $taskInfo
     * @param int      $workerId
     *
     * @return int|bool
     */
    public static function post(TaskInfo $taskInfo, int $workerId = -1)
    {
        return ServerManage::getServer('main')->getSwooleServer()->task($taskInfo, $workerId, [$taskInfo->getTaskHandler(), 'finish']);
    }

    /**
     * 使用任务名称投递异步任务
     * 调用成功返回任务ID，失败返回false.
     *
     * @param string $name
     * @param mixed  $data
     * @param int    $workerId
     *
     * @return int|bool
     */
    public static function nPost(string $name, $data, int $workerId = -1)
    {
        return static::post(self::getTaskInfo($name, $data), $workerId);
    }

    /**
     * 投递任务，协程挂起等待，单位：秒
     * 返回值为任务直接结果.
     *
     * @param TaskInfo $taskInfo
     * @param float    $timeout
     * @param int      $workerId
     *
     * @return string|bool
     */
    public static function postWait(TaskInfo $taskInfo, float $timeout, int $workerId = -1)
    {
        $server = ServerManage::getServer('main')->getSwooleServer();
        $result = $server->taskwait($taskInfo, $timeout, $workerId);
        $taskInfo->getTaskHandler()->finish($server, -1, $result);

        return $result;
    }

    /**
     * 使用任务名称投递任务，协程挂起等待，单位：秒
     * 返回值为任务直接结果.
     *
     * @param string $name
     * @param mixed  $data
     * @param float  $timeout
     * @param int    $workerId
     *
     * @return string|bool
     */
    public static function nPostWait(string $name, $data, float $timeout, int $workerId = -1)
    {
        return static::postWait(self::getTaskInfo($name, $data), $timeout, $workerId);
    }

    /**
     * 投递任务，协程方式等待全部执行完毕或超时，单位：秒
     * $tasks必须为数组，有两种情况
     * 1. TaskInfo数组
     * 2. ['task名称', 参数] 参数可以被省略.
     *
     * 返回值为任务直接结果
     *
     * @param TaskInfo[]|array $tasks
     * @param float            $timeout
     *
     * @return array
     */
    public static function postCo(array $tasks, float $timeout): array
    {
        $server = ServerManage::getServer('main')->getSwooleServer();
        foreach ($tasks as $i => $item)
        {
            if (!$item instanceof TaskInfo)
            {
                $tasks[$i] = self::getTaskInfo($item[0], $item[1] ?? []);
            }
        }
        $result = $server->taskCo($tasks, $timeout);
        foreach ($result as $i => $item)
        {
            $tasks[$i]->getTaskHandler()->finish($server, -1, $item);
        }

        return $result;
    }

    /**
     * 获取 TaskInfo.
     *
     * @param string $name
     * @param mixed  $data
     *
     * @return TaskInfo
     */
    public static function getTaskInfo(string $name, $data): TaskInfo
    {
        $task = TaskParser::getInstance()->getTask($name);
        $paramClass = $task['Task']->paramClass;

        return new TaskInfo(new BeanTaskHandler($task['className']), new $paramClass($data));
    }
}
