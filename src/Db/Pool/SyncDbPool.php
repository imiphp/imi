<?php
namespace Imi\Db\Pool;

use Imi\App;
use Imi\Bean\BeanFactory;
use Imi\Pool\BaseSyncPool;

/**
 * 同步数据库连接池
 */
class SyncDbPool extends BaseSyncPool
{
    /**
     * 创建资源
     * @return \Imi\Pool\Interfaces\IPoolResource
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();
        return new DbResource($this, BeanFactory::newInstance($config['dbClass'], $config));
    }
}