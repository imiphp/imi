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
     * 数据库操作类
     * @var mixed
     */
    protected $handlerClass;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        if(isset($resourceConfig['dbClass']))
        {
            $this->handlerClass = $resourceConfig['dbClass'];
        }
    }

    /**
     * 创建资源
     * @return \Imi\Pool\Interfaces\IPoolResource
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        return new DbResource($this, BeanFactory::newInstance($this->handlerClass, $this->resourceConfig));
    }
}