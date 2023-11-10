<?php

declare(strict_types=1);

namespace Imi\Swoole\Db\Pool;

use Imi\App;
use Imi\Db\Pool\DbResource;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\TUriResourceConfig;
use Imi\Swoole\Pool\BaseAsyncPool;

/**
 * Swoole协程MySQL的连接池.
 */
class CoroutineDbPool extends BaseAsyncPool
{
    use TUriResourceConfig;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, mixed $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * {@inheritDoc}
     */
    public function createNewResource(): IPoolResource
    {
        $config = $this->getNextResourceConfig();

        return new DbResource($this, App::newInstance($config['dbClass'] ?? 'PdoMysqlDriver', $config));
    }
}
