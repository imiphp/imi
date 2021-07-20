<?php

declare(strict_types=1);

namespace Imi\Swoole\Redis\Pool;

use Imi\Pool\TUriResourceConfig;
use Imi\Redis\Traits\TRedisPool;
use Imi\Swoole\Pool\BaseAsyncPool;

class CoroutineRedisPool extends BaseAsyncPool
{
    use TRedisPool;
    use TUriResourceConfig;

    /**
     * 数据库操作类.
     */
    protected string $handlerClass = \Redis::class;

    public function __construct(string $name, ?\Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }
}
