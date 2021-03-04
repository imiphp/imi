<?php

declare(strict_types=1);

namespace Imi\Server\Contract;

/**
 * 服务器工具类接口.
 */
interface IServerUtil
{
    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed          $data
     * @param int|int[]|null $fd         为 null 时，则发送给当前连接
     * @param string|null    $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public function send($data, $fd = null, $serverName = null): int;

    /**
     * 发送数据给指定标记的客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed                $data
     * @param string|string[]|null $flag       为 null 时，则发送给当前连接
     * @param string|null          $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public function sendByFlag($data, $flag = null, $serverName = null): int;

    /**
     * 发送数据给指定客户端，支持一个或多个（数组）.
     *
     * @param string         $data
     * @param int|int[]|null $fd         为 null 时，则发送给当前连接
     * @param string|null    $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public function sendRaw(string $data, $fd = null, ?string $serverName = null): int;

    /**
     * 发送数据给指定标记的客户端，支持一个或多个（数组）.
     *
     * @param string               $data
     * @param string|string[]|null $flag       为 null 时，则发送给当前连接
     * @param string|null          $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public function sendRawByFlag(string $data, $flag = null, $serverName = null): int;

    /**
     * 发送数据给所有客户端.
     *
     * 数据将会通过处理器编码
     *
     * @param mixed       $data
     * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     *
     * @return int
     */
    public function sendToAll($data, ?string $serverName = null, bool $toAllWorkers = true): int;

    /**
     * 发送数据给所有客户端.
     *
     * 数据原样发送
     *
     * @param string      $data
     * @param string|null $serverName   服务器名，默认为当前服务器或主服务器
     * @param bool        $toAllWorkers BASE模式下，发送给所有 worker 中的连接
     *
     * @return int
     */
    public function sendRawToAll(string $data, ?string $serverName = null, bool $toAllWorkers = true): int;

    /**
     * 发送数据给分组中的所有客户端，支持一个或多个（数组）.
     *
     * 数据将会通过处理器编码
     *
     * @param string|string[] $groupName
     * @param mixed           $data
     * @param string|null     $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public function sendToGroup($groupName, $data, ?string $serverName = null): int;

    /**
     * 发送数据给分组中的所有客户端，支持一个或多个（数组）.
     *
     * 数据原样发送
     *
     * @param string|string[] $groupName
     * @param string          $data
     * @param string|null     $serverName 服务器名，默认为当前服务器或主服务器
     *
     * @return int
     */
    public function sendRawToGroup($groupName, string $data, ?string $serverName = null): int;

    /**
     * 关闭一个或多个连接.
     *
     * @param int|int[]   $fd
     * @param string|null $serverName
     *
     * @return int
     */
    public function close($fd, ?string $serverName = null): int;

    /**
     * 关闭一个或多个指定标记的连接.
     *
     * @param string|string[] $flag
     * @param string|null     $serverName
     *
     * @return int
     */
    public function closeByFlag($flag, ?string $serverName = null): int;

    /**
     * 获取服务器.
     *
     * @param string|null $serverName
     *
     * @return \Imi\Server\Contract\IServer|null
     */
    public function getServer(?string $serverName = null): ?IServer;
}
