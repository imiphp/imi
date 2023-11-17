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
     * 是否启用统计
     */
    public function isEnableStatistics(): bool;

    /**
     * 获取额外配置.
     */
    public function getConfig(): array;
}
