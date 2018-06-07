<?php
namespace Imi\Redis;

use Imi\App;
use Imi\Util\Random;
use Imi\Bean\BeanFactory;
use Imi\Pool\BaseAsyncPool;

class RedisPool extends BaseAsyncPool
{
	/**
	 * 数据库操作类
	 * @var mixed
	 */
	protected $handlerClass = \Swoole\Coroutine\Redis::class;

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
	 * @return \Imi\Pool\Interfaces\IPoolResource
	 */
	protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
	{
		$db = BeanFactory::newInstance($this->handlerClass, [Random::letterAndNumber(8, 16)]);
		return new RedisResource($this, $db, $this->resourceConfig);
	}
}