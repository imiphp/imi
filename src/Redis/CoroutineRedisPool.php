<?php

namespace Imi\Redis;

use Imi\Bean\BeanFactory;
use Imi\Pool\BaseAsyncPool;
use Imi\Pool\TUriResourceConfig;

class CoroutineRedisPool extends BaseAsyncPool
{
    use TUriResourceConfig;

    /**
     * 数据库操作类.
     *
     * @var mixed
     */
    protected $handlerClass = \Redis::class;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * 创建资源.
     *
     * @return \Imi\Redis\RedisResource
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();
        $class = $config['handlerClass'] ?? $this->handlerClass;
        $db = BeanFactory::newInstance(RedisHandler::class, new $class());

        return new RedisResource($this, $db, $config);
    }
}
