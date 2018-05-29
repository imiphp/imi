<?php
namespace Imi\Db\Pool;

use Imi\Pool\Interfaces\IPoolResource;

/**
 * Swoole协程MySQL的连接资源
 */
class AsyncMysqlResource implements IPoolResource
{
	/**
	 * db对象
	 * @var \Swoole\MySQL
	 */
	private $db;

	/**
	 * 连接配置
	 * @var array
	 */
	private $config;

	public function __construct(\Swoole\MySQL $db, $config)
	{
		$this->db = $db;
		$this->config = $config;
	}

	/**
	 * 打开
	 * @return boolean
	 */
	public function open($callback = null)
	{
		$this->db->connect($this->config, $callback ?? function(){
			
		});
		return true;
	}

	/**
	 * 关闭
	 * @return void
	 */
	public function close()
	{
		$this->db->close();
	}

	/**
	 * 获取对象实例
	 * @return mixed
	 */
	public function getInstance()
	{
		return $this->db;
	}
}