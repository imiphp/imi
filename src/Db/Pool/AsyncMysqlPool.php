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
		return new AsyncMysqlResource(App::getBean($this->handlerClass), $this->resourceConfig);
		// return new \Imi\Db\Pool\AsyncMysqlResource(new \Swoole\Mysql, [
		// 	'host'		=> '192.168.0.110',
		// 	'user'		=> 'root',
		// 	'password'	=> 'root',
		// 	'database'	=> 'xincheng',
		// ]);;
		return new AsyncMysqlResource(new \Swoole\Mysql, $this->resourceConfig);
		return new AsyncMysqlResource($a, $this->resourceConfig);
	}
}