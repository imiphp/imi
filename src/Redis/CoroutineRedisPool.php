<?php
namespace Imi\Redis;

use Imi\App;
use Imi\Util\Random;
use Imi\Bean\BeanFactory;
use Imi\Pool\BaseAsyncPool;

class CoroutineRedisPool extends BaseAsyncPool
{
    /**
     * 数据库操作类
     * @var mixed
     */
    protected $handlerClass = Redis::class;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        if(isset($resourceConfig['handlerClass']))
        {
            $this->handlerClass = $resourceConfig['handlerClass'];
        }
    }

    /**
     * 创建资源
     * @return CoroutineRedisResource
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $db = BeanFactory::newInstance($this->handlerClass);
        return new CoroutineRedisResource($this, $db, $this->getNextResourceConfig());
    }
}