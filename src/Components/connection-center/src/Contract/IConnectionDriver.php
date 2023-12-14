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
    public static function createConnectionConfig(string|array $config): IConnectionConfig;

    public function __construct(IConnectionManagerConfig $connectionManagerConfig, IConnectionLoadBalancer $connectionLoadBalancer);

    /**
     * 获取连接管理器配置.
     */
    public function getConnectionManagerConfig(): IConnectionManagerConfig;

    /**
     * 获取连接负载均衡器.
     */
    public function getConnectionLoadBalancer(): IConnectionLoadBalancer;

    /**
     * 创建新连接.
     */
    public function createInstance(): object;

    /**
     * 连接.
     *
     * 返回连接对象（也可能是原对象）
     */
    public function connect(object $instance): object;

    /**
     * 关闭.
     */
    public function close(object $instance): void;

    /**
     * 重置资源，当资源被释放后重置一些默认的设置.
     */
    public function reset(object $instance): void;

    /**
     * 检查是否可用.
     * 此操作是实时检查，能实时返回真实的结果.
     */
    public function checkAvailable(object $instance): bool;

    /**
     * 发送心跳.
     */
    public function ping(object $instance): bool;
}
