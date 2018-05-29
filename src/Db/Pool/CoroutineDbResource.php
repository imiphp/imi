<?php
namespace Imi\Db\Pool;

use Imi\Db\Interfaces\IDb;
use Imi\Pool\Interfaces\IPoolResource;

/**
 * Swoole协程MySQL的连接资源
 */
class CoroutineDbResource implements IPoolResource
{
	/**
	 * db对象
	 * @var IDb
	 */
	private $db;

	public function __construct(IDb $db)
	{
		$this->db = $db;
	}

	/**
	 * 打开
	 * @return boolean
	 */
	public function open()
	{
		return $this->db->open();
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