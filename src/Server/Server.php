<?php

declare(strict_types=1);

namespace Imi\Server;

use Imi\RequestContext;
use Imi\Server\Contract\IServer;
use Imi\Server\Contract\IServerUtil;

/**
 * 服务器工具类.
 */
class Server
{
    private function __construct()
    {
    }

    /**
     * 获取上下文管理器实例.
     */
    public static function getInstance(?string $serverName = null): IServerUtil
    {
        // @phpstan-ignore-next-line
        return self::getServer($serverName)->getBean('ServerUtil');
    }

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed                          $data
     * @param int|int[]|string|string[]|null $clientId     为 null 时，则发送给当前连接
     * @param string|null                    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function send($data, $clientId = null, $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->send($data, $clientId, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给指定标记的客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed                $data
     * @param string|string[]|null $flag         为 null 时，则发送给当前连接
     * @param string|null          $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function sendByFlag($data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->sendByFlag($data, $flag, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * @param int|int[]|string|string[]|null $clientId     为 null 时，则发送给当前连接
     * @param string|null                    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function sendRaw(string $data, $clientId = null, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->sendRaw($data, $clientId, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给指定标记的客户端，支持一个或多个（数组）.
     *
     * @param string|string[]|null $flag         为 null 时，则发送给当前连接
     * @param string|null          $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function sendRawByFlag(string $data, $flag = null, $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->sendRawByFlag($data, $flag, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给所有客户端.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed       $data
     * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function sendToAll($data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->sendToAll($data, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给所有客户端.
     *
     * 数据原样发送
     *
     * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function sendRawToAll(string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->sendRawToAll($data, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给分组中的所有客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param string|string[] $groupName
     * @param mixed           $data
     * @param string|null     $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool            $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function sendToGroup($groupName, $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->sendToGroup($groupName, $data, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给分组中的所有客户端，支持一个或多个（数组）.
     *
     * 数据原样发送
     *
     * @param string|string[] $groupName
     * @param string|null     $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool            $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function sendRawToGroup($groupName, string $data, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->sendRawToGroup($groupName, $data, $serverName, $toAllWorkers);
    }

    /**
     * 关闭一个或多个连接.
     *
     * @param int|int[]|string|string[]|null $clientId
     * @param bool                           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function close($clientId, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->close($clientId, $serverName, $toAllWorkers);
    }

    /**
     * 关闭一个或多个指定标记的连接.
     *
     * @param string|string[]|null $flag
     * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance($serverName)->closeByFlag($flag, $serverName, $toAllWorkers);
    }

    /**
     * 连接是否存在.
     *
     * @param string|int|null $clientId
     */
    public static function exists($clientId, ?string $serverName = null, bool $toAllWorkers = true): bool
    {
        return static::getInstance($serverName)->exists($clientId, $serverName, $toAllWorkers);
    }

    /**
     * 指定标记的连接是否存在.
     */
    public static function flagExists(?string $flag, ?string $serverName = null, bool $toAllWorkers = true): bool
    {
        return static::getInstance($serverName)->flagExists($flag, $serverName, $toAllWorkers);
    }

    /**
     * 获取连接数组.
     *
     * 有可能返回的是当前进程管理的连接
     */
    public static function getConnections(?string $serverName = null): array
    {
        return static::getInstance($serverName)->getConnections();
    }

    /**
     * 获取当前连接数量.
     */
    public static function getConnectionCount(?string $serverName = null): int
    {
        return static::getInstance($serverName)->getConnectionCount();
    }

    /**
     * 获取服务器.
     */
    public static function getServer(?string $serverName = null): ?IServer
    {
        if (null === $serverName)
        {
            return RequestContext::getServer() ?? ServerManager::getServer('main');
        }
        else
        {
            return ServerManager::getServer($serverName);
        }
    }
}
