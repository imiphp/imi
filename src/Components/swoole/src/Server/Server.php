<?php

declare(strict_types=1);

namespace Imi\Swoole\Server;

use Imi\Swoole\Server\Contract\ISwooleServerUtil;

/**
 * 服务器工具类.
 *
 * @method static ISwooleServerUtil getInstance(?string $serverName = null)
 */
class Server extends \Imi\Server\Server
{
    /**
     * 发送消息给 Worker 进程，使用框架内置格式.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public static function sendMessage(string $action, array $data = [], int|array|null $workerId = null, ?string $serverName = null): int
    {
        return static::getInstance($serverName)->sendMessage($action, $data, $workerId);
    }

    /**
     * 发送消息给 Worker 进程.
     *
     * 返回成功发送消息数量
     *
     * @param int|int[]|null $workerId
     */
    public static function sendMessageRaw(string $message, int|array|null $workerId = null, ?string $serverName = null): int
    {
        return static::getInstance($serverName)->sendMessageRaw($message, $workerId);
    }
}
