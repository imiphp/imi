<?php
namespace Imi\Db\Pool;

use Imi\Db\Interfaces\IDb;
use Imi\Pool\BasePoolResource;
use Imi\Pool\Interfaces\IPoolResource;

/**
 * Swoole协程MySQL的连接资源
 */
class DbResource extends BasePoolResource
{
	/**
	 * db对象
	 * @var IDb
	 */
	private $db;

	public function __construct(\Imi\Pool\Interfaces\IPool $pool, IDb $db)
	{
		parent::__construct($pool);
		$this->db = $db;
	}

	/**
	 * 打开
	 * @return boolean
	 */
	public function open($callback = null)
	{
		$this->db->open();
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