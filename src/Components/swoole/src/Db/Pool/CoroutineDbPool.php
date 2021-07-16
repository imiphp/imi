<?php

declare(strict_types=1);

namespace Imi\Swoole\Db\Pool;

use Imi\App;
use Imi\Db\Pool\DbResource;
use Imi\Pool\TUriResourceConfig;
use Imi\Swoole\Pool\BaseAsyncPool;

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
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();

        return new DbResource($this, App::getBean($config['dbClass'] ?? 'PdoMysqlDriver', $config));
    }
}
