<?php
namespace Imi\Db\CoroutineMysql;

use Imi\Db\Interfaces\IDb;
use Imi\Db\Traits\SqlParser;
use Imi\Db\Interfaces\IStatement;
use Imi\Pool\Interfaces\IPoolResource;

/**
 * Swoole协程MySQL驱动
 */
class Driver implements IDb
{
	use SqlParser;

	/**
	 * 连接对象
	 * @var \Swoole\Coroutine\MySQL
	 */
	protected $instance;

	/**
	 * 连接配置
	 * @var array
	 */
	protected $option;

	/**
	 * 是否在一个事务内
	 * @var boolean
	 */
	protected $inTransaction = false;

	/**
	 * 参数格式：
	 * [
	 * 'host' => 'MySQL IP地址',
	 * 'user' => '数据用户',
	 * 'password' => '数据库密码',
	 * 'database' => '数据库名',
	 * 'port'    => 'MySQL端口 默认3306 可选参数',
	 * 'timeout' => '建立连接超时时间',
	 * 'charset' => '字符集',
	 * 'strict_type' => false, //开启严格模式，返回的字段将自动转为数字类型
	 * ]
	 * @param array $option
	 */
	public function __construct($option = [])
	{
		$this->option = $option;
		$this->instance = new \Swoole\Coroutine\MySQL();
	}

	/**
	 * 打开
	 * @return boolean
	 */
	public function open()
	{
		return $this->instance->connect($this->option);
	}

	/**
	 * 关闭
	 * @return void
	 */
	public function close()
	{
		$this->instance->close();
	}

	/**
	 * 获取原对象实例
	 * @return \Swoole\Coroutine\MySQL
	 */
	public function getInstance(): \Swoole\Coroutine\MySQL
	{
		return $this->instance;
	}

	/**
	 * 启动一个事务
	 * @return boolean
	 */
	public function beginTransaction(): bool
	{
		$this->inTransaction = true;
		return $this->instance->begin();
	}

	/**
	 * 提交一个事务
	 * @return boolean
	 */
	public function commit(): bool
	{
		$this->inTransaction = false;
		return $this->instance->commit();
	}

	/**
	 * 回滚一个事务
	 * @return boolean
	 */
	public function rollBack(): bool
	{
		$this->inTransaction = false;
		return $this->instance->rollback();
	}

	/**
	 * 检查是否在一个事务内
	 * @return bool
	 */
	public function inTransaction(): bool
	{
		return $this->inTransaction;
	}

	/**
	 * 返回错误码
	 * @return mixed
	 */
	public function errorCode()
	{
		return $this->instance->errno;
	}
	
	/**
	 * 返回错误信息
	 * @return array
	 */
	public function errorInfo(): array
	{
		return [$this->instance->error];
	}

	/**
	 * 执行一条 SQL 语句，并返回受影响的行数
	 * @param string $sql
	 * @return integer
	 */
	public function exec(string $sql): int
	{
		$result = $this->instance->query($sql);
		if(false === $result)
		{
			return 0;
		}
		else
		{
			return $this->instance->affected_rows;
		}
	}

	/**
	 * 取回一个数据库连接的属性
	 * @param mixed $attribute
	 * @return mixed
	 */
	public function getAttribute($attribute)
	{
		return null;
	}

	/**
	 * 设置属性
	 * @param mixed $attribute
	 * @param mixed $value
	 * @return bool
	 */
	public function setAttribute($attribute, $value)
	{
		return true;
	}

	/**
	 * 返回最后插入行的ID或序列值
	 * @param string $name
	 * @return string
	 */
	public function lastInsertId(string $name = null)
	{
		return $this->instance->lastInsertId;
	}

	/**
	 * 准备执行语句并返回一个语句对象
	 * @param string $sql
	 * @param array $driverOptions
	 * @return IStatement|bool
	 */
	public function prepare(string $sql, array $driverOptions = [])
	{
		// 处理支持 :xxx 参数格式
		$execSql = $this->parseSqlNameParamsToQuestionMark($sql, $params);
		$stmt = $this->instance->prepare($execSql);
		if(false === $stmt)
		{
			return false;
		}
		return new Statement($stmt, $sql, $params);
	}

	/**
	 * 执行一条SQL语句，返回一个结果集作为PDOStatement对象
	 * @param string $sql
	 * @return IStatement|bool
	 */
	public function query(string $sql)
	{
		$stmt = $this->instance->prepare($sql);
		if(false === $stmt)
		{
			return false;
		}
		$data = $stmt->execute([]);
		return new Statement($stmt, $sql, [], $data);
	}
}