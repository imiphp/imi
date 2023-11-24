<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

/**
 * 连接管理器.
 */
interface IConnectionManager
{
    public static function createConfig(array $config): IConnectionManagerConfig;

    public function __construct(IConnectionManagerConfig $config);

    public function getConfig(): IConnectionManagerConfig;

    public function getDriver(): IConnectionDriver;

    /**
     * 创建新连接.
     */
    public function createConnection(): IConnection;

    /**
     * 获取连接.
     */
    public function getConnection(): IConnection;

    /**
     * 释放连接.
     */
    public function releaseConnection(IConnection $connection): void;

    /**
     * 连接与连接管理器分离，作为一个独立连接.
     */
    public function detachConnection(IConnection $connection): void;

    /**
     * 关闭连接管理器.
     *
     * 会关闭连接管理器中所有连接
     */
    public function close(): void;

    /**
     * 是否可用.
     */
    public function isAvailable(): bool;

    /**
     * 获取状态
     */
    public function getStatistics(): IConnectionManagerStatistics;
}
