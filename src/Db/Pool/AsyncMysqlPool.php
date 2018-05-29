<?php
namespace Imi\Db\Pool;

use Imi\App;
use Imi\Pool\BaseAsyncPool;

/**
 * Swoole 异步 MySQL 连接池
 */
class AsyncMysqlPool extends BaseAsyncPool
{
	/**
	 * 数据库操作类
	 * @var mixed
	 */
	protected $handlerClass = \Swoole\Mysql::class;

	public function __construct(\Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
	{
		parent::__construct($config, $resourceConfig);
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
		$db = clone App::getBean($this->handlerClass);
		return new AsyncMysqlResource($db, $this->resourceConfig);
	}
}