<?php
namespace Imi\Redis;

use Imi\App;
use Imi\Pool\BaseAsyncPool;

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
		$db = clone App::getBean($this->handlerClass);
		return new RedisResource($this, $db, $this->resourceConfig);
	}
}