<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

/**
 * 连接管理器状态.
 */
interface IConnectionManagerStatistics extends \JsonSerializable
{
    /**
     * 获取创建连接次数.
     */
    public function getCreateConnectionTimes(): int;

    /**
     * 获取获取连接次数.
     */
    public function getGetConnectionTimes(): int;

    /**
     * 获取释放连接次数.
     */
    public function getReleaseConnectionTimes(): int;

    /**
     * 获取当前连接数量.
     */
    public function getTotalConnectionCount(): int;

    /**
     * 获取空闲连接数量.
     */
    public function getFreeConnectionCount(): int;

    /**
     * 获取被占用连接数量.
     */
    public function getUsedConnectionCount(): int;

    /**
     * 获取最大获取连接耗时.
     *
     * 单位：秒
     */
    public function getMaxGetConnectionTime(): float;

    /**
     * 获取最小获取连接耗时.
     *
     * 单位：秒
     */
    public function getMinGetConnectionTime(): float;

    /**
     * 获取最后一次获取连接耗时.
     *
     * 单位：秒
     */
    public function getLastGetConnectionTime(): float;
}
