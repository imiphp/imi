<?php
namespace Imi\Redis;

use Imi\App;
use Imi\Pool\BaseAsyncPool;
use Imi\Util\Random;

class RedisPool extends BaseAsyncPool
{
	/**
	 * 数据库操作类
	 * @var mixed
	 */
	protected $handlerClass = \Swoole\Coroutine\Redis::class;

	public function __construct(\Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
	{
		parent::__construct($config, $resourceConfig);
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
		$db = App::getBean($this->handlerClass, [Random::letterAndNumber(8, 16)]);
		return new RedisResource($this, $db, $this->resourceConfig);
	}
}