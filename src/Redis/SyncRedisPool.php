<?php

declare(strict_types=1);

namespace Imi\Redis;

use Imi\Pool\BaseSyncPool;
use Imi\Pool\TUriResourceConfig;
use Imi\Redis\Traits\TRedisPool;

class SyncRedisPool extends BaseSyncPool
{
    use TRedisPool;
    use TUriResourceConfig;

    /**
     * 数据库操作类.
     */
    protected string $handlerClass = \Redis::class;

    /**
     * @param \Imi\Pool\Interfaces\IPoolConfig $config
     * @param mixed                            $resourceConfig
     */
    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }
}
