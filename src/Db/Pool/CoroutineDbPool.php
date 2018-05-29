<?php
namespace Imi\Db\Pool;

use Imi\Pool\BaseAsyncPool;
use Imi\App;

/**
 * Swoole协程MySQL的连接池
 */
class CoroutineDbPool extends BaseAsyncPool
{
	/**
	 * 数据库操作类
	 * @var mixed
	 */
	protected $handlerClass;

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
		return new DbResource(App::getBean($this->handlerClass, $this->resourceConfig));
	}
}