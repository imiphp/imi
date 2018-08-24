<?php
namespace Imi\Db\Query;

use Imi\Model\Model;
use Imi\Bean\BeanFactory;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Query\Interfaces\IResult;

class Result implements IResult
{
	/**
	 * Statement
	 * @var IStatement
	 */
	private $statement;

	/**
	 * 是否执行成功
	 * @var bool
	 */
	private $isSuccess;

	/**
	 * 查询结果类的类名，为null则为数组
	 * @var string
	 */
	private $modelClass;

	public function __construct($statement, $modelClass = null)
	{
		$this->modelClass = $modelClass;
		if($statement instanceof IStatement)
		{
			$this->statement = clone $statement;
			$this->isSuccess = '' === $this->statement->errorInfo();
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
	 * 返回一行数据，数组或对象，失败返回null
	 * @param string $className 实体类名，为null则返回数组
	 * @return mixed|null
	 */
	public function get($className = null)
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		$result = $this->statement->fetch();
		if(false === $result)
		{
			return null;
		}

		if(null === $className)
		{
			$className = $this->modelClass;
		}
		if(null === $className)
		{
			return $result;
		}
		else
		{
			if(is_subclass_of($className, Model::class))
			{
				$object = BeanFactory::newInstance($className, $result);
			}
			else
			{
				$object = BeanFactory::newInstance($className);
				foreach($result as $k => $v)
				{
					$object->$k = $v;
				}
			}
			return $object;
		}
	}

	/**
	 * 返回数组，失败返回null
	 * @param string $className 实体类名，为null则数组每个成员为数组
	 * @return array|null
	 */
	public function getArray($className = null)
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		$result = $this->statement->fetchAll();
		if(false === $result)
		{
			return null;
		}

		if(null === $className)
		{
			$className = $this->modelClass;
		}
		if(null === $className)
		{
			return $result;
		}
		else
		{
			$list = [];
			$isModelClass = is_subclass_of($className, Model::class);
			foreach($result as $item)
			{
				if($isModelClass)
				{
					$object = BeanFactory::newInstance($className, $item);
				}
				else
				{
					$object = $item;
				}
				$list[] = $object;
			}
			return $list;
		}
	}

	/**
	 * 获取一列数据
	 * @return array
	 */
	public function getColumn($column = 0)
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		return $this->statement->fetchAll(\PDO::FETCH_COLUMN, $column);
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
	
	/**
	 * 获取记录行数
	 * @return int
	 */
	public function getRowCount()
	{
		if(!$this->isSuccess)
		{
			throw new \RuntimeException('Result is not success!');
		}
		return count($this->statement->fetchAll());
	}

	/**
	 * 获取执行的SQL语句
	 *
	 * @return string
	 */
	public function getSql()
	{
		return $this->statement->getSql();
	}

	/**
	 * 获取结果集对象
	 *
	 * @return \Imi\Db\Interfaces\IStatement
	 */
	public function getStatement(): IStatement
	{
		return $this->statement;
	}
}