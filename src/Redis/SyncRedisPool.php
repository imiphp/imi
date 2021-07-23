<?php

namespace Imi\Redis;

use Imi\Pool\BaseSyncPool;
use Imi\Pool\TUriResourceConfig;
use Imi\Redis\Traits\TRedisPool;

class SyncRedisPool extends BaseSyncPool
{
    use TRedisPool;
    use TUriResourceConfig;

    /**
     * @param string                                $name
     * @param \Imi\Pool\Interfaces\IPoolConfig|null $config
     * @param array|null                            $resourceConfig
     */
    public function __construct(string $name, ?\Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }
}
