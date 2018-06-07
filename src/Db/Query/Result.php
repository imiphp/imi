<?php
namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Interfaces\IStatement;

class Result implements IResult
{
	/**
	 * Statement
	 * @var IStatement
	 */
	private $statement;

	private $isSuccess;

	public function __construct($statement)
	{
		if($statement instanceof IStatement)
		{
			$this->statement = clone $statement;
			$this->isSuccess = [] === $this->statement->errorInfo();
		}
		else
		{
			$this->isSuccess = false;
		}
	}

	/**
	 * SQL是否执行成功
	 * @return boolean
	 */
	public function isSuccess(): bool
	{
		return $this->isSuccess;
	}

	/**
	 * 获取最后插入的ID
	 * @return string
	 */
	public function getLastInsertId()
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		return $this->statement->lastInsertId();
	}

	/**
	 * 获取影响行数
	 * @return int
	 */
	public function getAffectedRows()
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		return $this->statement->rowCount();
	}

	/**
	 * 返回一行数据，数组或对象
	 * @param string $className 实体类名，为null则返回数组
	 * @return mixed
	 */
	public function get($className = null)
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		$result = $this->statement->fetch();
		if(null === $className)
		{
			return $result;
		}
		else
		{
			$object = new $className;
			foreach($result as $k => $v)
			{
				$object->$k = $v;
			}
			return $object;
		}
	}

	/**
	 * 返回数组
	 * @param string $className 实体类名，为null则数组每个成员为数组
	 * @return array
	 */
	public function getArray($className = null)
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		$result = $this->statement->fetchAll();
		if(null === $className)
		{
			return $result;
		}
		else
		{
			$list = [];
			foreach($result as $item)
			{
				foreach($item as $k => $v)
				{
					$object = new $className;
					$object->$k = $v;
				}
			}
			return $list;
		}
	}

	/**
	 * 获取标量结果
	 * @param integer|string $columnKey
	 * @return mixed
	 */
	public function getScalar($columnKey = 0)
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		return $this->statement->fetchColumn();
	}
}