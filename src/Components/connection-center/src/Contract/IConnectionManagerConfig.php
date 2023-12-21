<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

/**
 * 连接管理器配置.
 */
interface IConnectionManagerConfig
{
    /**
     * 获取连接生命周期管理器类名.
     */
    public function getDriver(): string;

    /**
     * 获取负载均衡器.
     */
    public function getLoadBalancer(): string;

    /**
     * 是否启用统计
     */
    public function isEnableStatistics(): bool;

    /**
     * 获取额外配置.
     */
    public function getConfig(): array;

    /**
     * 获取请求上下文资源检查状态间隔，单位：支持小数的秒.
     *
     * 为 null/0 则每次都检查
     */
    public function getRequestResourceCheckInterval(): ?float;

    /**
     * 获取是否在获取资源时检查状态
     */
    public function isCheckStateWhenGetResource(): bool;
}
