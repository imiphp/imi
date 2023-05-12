<?php

declare(strict_types=1);

namespace Imi\Redis\Enum;

class RedisMode
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 单机.
     */
    public const STANDALONE = 'standalone';

    /**
     * 哨兵模式.
     */
    public const SENTINEL = 'sentinel';

    /**
     * 集群模式.
     */
    public const CLUSTER = 'cluster';
}
