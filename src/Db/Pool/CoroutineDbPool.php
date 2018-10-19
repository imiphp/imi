<?php
namespace Imi\Db\Pool;

use Imi\App;
use Imi\Bean\BeanFactory;
use Imi\Pool\BaseAsyncPool;

/**
 * Swoole协程MySQL的连接池
 */
class CoroutineDbPool extends BaseAsyncPool
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