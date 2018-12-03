<?php
namespace Imi\Redis;

use Imi\App;
use Imi\Util\Random;
use Imi\Bean\BeanFactory;
use Imi\Pool\BaseSyncPool;
use Imi\Pool\TUriResourceConfig;

class SyncRedisPool extends BaseSyncPool
{
    use TUriResourceConfig;

    /**
     * 数据库操作类
     * @var mixed
     */
    protected $handlerClass = \Redis::class;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * 创建资源
     * @return SyncRedisResource
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();
        $db = BeanFactory::newInstance($config['handlerClass'] ?? $this->handlerClass);
        return new SyncRedisResource($this, $db, $config);
    }
}