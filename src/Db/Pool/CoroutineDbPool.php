<?php

namespace Imi\Db\Pool;

use Imi\Bean\BeanFactory;
use Imi\Pool\BaseAsyncPool;
use Imi\Pool\TUriResourceConfig;

/**
 * Swoole协程MySQL的连接池.
 */
class CoroutineDbPool extends BaseAsyncPool
{
    use TUriResourceConfig;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * 创建资源.
     *
     * @return \Imi\Pool\Interfaces\IPoolResource
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();

        return new DbResource($this, BeanFactory::newInstance($config['dbClass'] ?? \Imi\Db\Drivers\PdoMysql\Driver::class, $config));
    }
}
