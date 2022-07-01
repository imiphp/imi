<?php

declare(strict_types=1);

namespace Imi\Db\Pool;

use Imi\App;
use Imi\Pool\BaseSyncPool;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\TUriResourceConfig;

/**
 * 同步数据库连接池.
 */
class SyncDbPool extends BaseSyncPool
{
    use TUriResourceConfig;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * {@inheritDoc}
     */
    protected function createResource(): IPoolResource
    {
        return $this->createNewResource();
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
