<?php

declare(strict_types=1);

namespace Imi\Swoole\Task;

use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Task\Handler\BeanTaskHandler;

class TaskManager
{
    use \Imi\Util\Traits\TStaticClass;

    private static array $map = [];

    public static function getMap(): array
    {
        return self::$map;
    }

    public static function setMap(array $map): void
    {
        self::$map = $map;
    }

    /**
     * 增加映射关系.
     */
    public static function add(string $name, string $className, array $options): void
    {
        self::$map[$name] = [
            'className' => $className,
            'options'   => $options,
        ];
    }

    /**
     * 获取配置.
     */
    public static function get(string $name): ?array
    {
        return self::$map[$name] ?? null;
    }

    /**
     * 投递异步任务
     * 调用成功返回任务ID，失败返回false.
     *
     * @return int|bool
     */
    public static function post(TaskInfo $taskInfo, int $workerId = -1)
    {
        return ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer()->task($taskInfo, $workerId, [$taskInfo->getTaskHandler(), 'finish']);
    }

    /**
     * 使用任务名称投递异步任务
     * 调用成功返回任务ID，失败返回false.
     *
     * @param mixed $data
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
     * @return mixed
     */
    public static function postWait(TaskInfo $taskInfo, float $timeout, int $workerId = -1)
    {
        $server = ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer();
        $result = $server->taskwait($taskInfo, $timeout, $workerId);
        $taskInfo->getTaskHandler()->finish($server, -1, $result);

        return $result;
    }

    /**
     * 使用任务名称投递任务，协程挂起等待，单位：秒
     * 返回值为任务直接结果.
     *
     * @param mixed $data
     *
     * @return mixed
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
     */
    public static function postCo(array $tasks, float $timeout): array
    {
        $server = ServerManager::getServer('main', ISwooleServer::class)->getSwooleServer();
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
     * @param mixed $data
     */
    public static function getTaskInfo(string $name, $data): TaskInfo
    {
        $task = self::get($name);
        $paramClass = $task['options']['paramClass'];

        return new TaskInfo(new BeanTaskHandler($task['className']), new $paramClass($data));
    }
}
