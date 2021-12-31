<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Imi\Config;
use Imi\Util\Imi;
use Imi\Workerman\Server\Contract\IWorkermanServerUtil;
use Workerman\Worker;

/**
 * 服务器工具类.
 *
 * @method static IWorkermanServerUtil getInstance(?string $serverName = null)
 */
class Server extends \Imi\Server\Server
{
    /**
     * 初始化 Workerman 的 Worker 类.
     */
    public static function initWorkermanWorker(?string $serverName = null): void
    {
        $config = Config::get('@app.workerman.worker', []);
        foreach ($config as $key => $value)
        {
            WorkermanServerWorker::$$key = $value;
        }
        if ('' === WorkermanServerWorker::$pidFile)
        {
            WorkermanServerWorker::$pidFile = Imi::getRuntimePath(null === $serverName ? 'workerman-server.pid' : ('workerman-server-' . $serverName . '.pid'));
        }
    }

    /**
     * 发送消息给 Worker 进程，使用框架内置格式.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public static function sendMessage(string $action, array $data = [], $workerId = null, ?string $serverName = null): int
    {
        return static::getInstance($serverName)->sendMessage($action, $data, $workerId, $serverName);
    }

    /**
     * 发送消息给 Worker 进程.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public static function sendMessageRaw(array $data, $workerId = null, ?string $serverName = null): int
    {
        return static::getInstance($serverName)->sendMessageRaw($data, $workerId, $serverName);
    }
}
