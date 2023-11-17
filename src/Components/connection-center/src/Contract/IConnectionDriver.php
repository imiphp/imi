<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

/**
 * 连接驱动.
 */
interface IConnectionDriver
{
    /**
     * 创建连接配置.
     */
    public static function createConnectionConfig(array $config): IConnectionConfig;

    public function __construct(IConnectionConfig $config);

    /**
     * 设置连接配置.
     */
    public function setConnectionConfig(IConnectionConfig $config): self;

    /**
     * 获取连接配置.
     */
    public function getConnectionConfig(): IConnectionConfig;

    /**
     * 创建新连接.
     */
    public function createInstance(): mixed;

    /**
     * 连接.
     *
     * 返回连接对象（也可能是原对象）
     */
    public function connect(mixed $instance): mixed;

    /**
     * 关闭.
     */
    public function close(mixed $instance): void;

    /**
     * 重置资源，当资源被释放后重置一些默认的设置.
     */
    public function reset(mixed $instance): void;

    /**
     * 检查是否可用.
     * 此操作是实时检查，能实时返回真实的结果.
     */
    public function checkAvailable(mixed $instance): bool;

    /**
     * 发送心跳.
     */
    public function ping(mixed $instance): bool;
}
