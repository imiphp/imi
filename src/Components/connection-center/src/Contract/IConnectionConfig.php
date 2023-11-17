<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Contract;

/**
 * 连接配置.
 */
interface IConnectionConfig
{
    public static function createFromArray(array $config): self;
}
