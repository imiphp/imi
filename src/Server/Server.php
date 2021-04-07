<?php

declare(strict_types=1);

namespace Imi\Server;

use Imi\App;
use Imi\Config;
use Imi\Server\Contract\IServer;
use Imi\Server\Contract\IServerUtil;
use InvalidArgumentException;

/**
 * 服务器工具类.
 */
class Server
{
    /**
     * 服务器工具类对象.
     */
    private static IServerUtil $serverUtil;

    private function __construct()
    {
    }

    /**
     * 获取上下文管理器实例.
     */
    public static function getInstance(): IServerUtil
    {
        if (!isset(static::$serverUtil))
        {
            $contextClass = Config::get('@app.imi.ServerUtil');
            if (null === $contextClass)
            {
                throw new InvalidArgumentException('Config "@app.imi.ServerUtil" not found');
            }

            return static::$serverUtil = App::getBean($contextClass);
        }

        return static::$serverUtil;
    }

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed          $data
     * @param int|int[]|null $clientId     为 null 时，则发送给当前连接
     * @param string|null    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function send($data, $clientId = null, $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance()->send($data, $clientId, $serverName, $toAllWorkers);
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
        return static::getInstance()->sendByFlag($data, $flag, $serverName, $toAllWorkers);
    }

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * @param int|int[]|null $clientId     为 null 时，则发送给当前连接
     * @param string|null    $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function sendRaw(string $data, $clientId = null, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance()->sendRaw($data, $clientId, $serverName, $toAllWorkers);
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
        return static::getInstance()->sendRawByFlag($data, $flag, $serverName, $toAllWorkers);
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
        return static::getInstance()->sendToAll($data, $serverName, $toAllWorkers);
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
        return static::getInstance()->sendRawToAll($data, $serverName, $toAllWorkers);
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
        return static::getInstance()->sendToGroup($groupName, $data, $serverName, $toAllWorkers);
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
        return static::getInstance()->sendRawToGroup($groupName, $data, $serverName, $toAllWorkers);
    }

    /**
     * 关闭一个或多个连接.
     *
     * @param int|int[]|null $clientId
     * @param bool           $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function close($clientId, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance()->close($clientId, $serverName, $toAllWorkers);
    }

    /**
     * 关闭一个或多个指定标记的连接.
     *
     * @param string|string[]|null $flag
     * @param bool                 $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     */
    public static function closeByFlag($flag, ?string $serverName = null, bool $toAllWorkers = true): int
    {
        return static::getInstance()->closeByFlag($flag, $serverName, $toAllWorkers);
    }

    /**
     * 获取服务器.
     */
    public static function getServer(?string $serverName = null): ?IServer
    {
        return static::getInstance()->getServer($serverName);
    }
}
