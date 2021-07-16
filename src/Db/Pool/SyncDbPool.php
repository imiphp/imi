<?php

declare(strict_types=1);

namespace Imi\Db\Pool;

use Imi\App;
use Imi\Pool\BaseSyncPool;
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
     * 创建资源.
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();

        return new DbResource($this, App::getBean($config['dbClass'] ?? 'PdoMysqlDriver', $config));
    }
}
