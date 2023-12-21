<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

/**
 * 连接配置.
 */
interface IConnectionConfig
{
    public static function create(string|array $config): self;

    public static function parseStringConfig(string $string): array;

    /**
     * 权重.
     */
    public function getWeight(): float;
}
