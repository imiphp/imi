<?php
namespace Imi\Model;

use Imi\Db\Db;
use Imi\Util\Call;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Util\Interfaces\IArrayable;
use Imi\Db\Query\Interfaces\IResult;

class Model implements \Iterator, \ArrayAccess, IArrayable
{
	private $__fieldNames;

	public function __construct($data = [])
	{
		foreach($data as $k => $v)
		{
			$this->$k = $v;
		}
		$this->__fieldNames = ModelManager::getFieldNames($this);
	}

	/**
	 * 返回一个查询器
	 * @return \Imi\Db\Query\Interfaces\IQuery
	 */
	public static function query()
	{
		return Db::query(ModelManager::getDbPoolName(static::class), static::class)->table(ModelManager::getTable(static::class));
	}

	/**
	 * 查找一条记录
	 * @param callable|mixed ...$ids
	 * @return static
	 */
	public static function find(...$ids)
	{
		if(!isset($ids[0]))
		{
			return null;
		}
		$query = static::query();
		if(is_callable($ids[0]))
		{
			// 回调传入条件
			Call::callUserFunc($ids[0], $query);
		}
		else
		{
			// 传主键值
			if(is_array($ids[0]))
			{
				// 键值数组where条件
				foreach($ids[0] as $k => $v)
				{
					$query->where($k, '=', $v);
				}
			}
			else
			{
				// 主键值
				$id = ModelManager::getId(static::class);
				if(is_string($id))
				{
					$id = [$id];
				}
				foreach($id as $i => $idName)
				{
					if(!isset($ids[$i]))
					{
						break;
					}
					$query->where($idName, '=', $ids[$i]);
				}
			}
		}
		return $query->select()->get();
	}

	/**
	 * 查询多条记录
	 * @param callable $queryCallable
	 * @return static[]
	 */
	public static function select(callable $queryCallable = null)
	{
		$query = static::query();
		if(null !== $queryCallable)
		{
			// 回调传入条件
			Call::callUserFunc($queryCallable, $query);
		}
		return $query->select()->getArray();
	}

	/**
	 * 插入记录
	 * @return IResult
	 */
	public function insert(): IResult
	{
		return static::query()->insert($this);
	}

	/**
	 * 更新记录
	 * @return IResult
	 */
	public function update(): IResult
	{
		$query = static::query();
		return $this->parseWhereId($query)->update($this);
	}

	/**
	 * 保存记录
	 * @return IResult
	 */
	public function save(): IResult
	{
		$query = static::query();
		$selectResult = $this->parseWhereId($query)->select();
		if($selectResult->getRowCount() > 0)
		{
			// 更新
			return $this->update();
		}
		else
		{
			// 插入
			return $this->insert();
		}
	}

	/**
	 * 删除记录
	 * @return IResult
	 */
	public function delete(): IResult
	{
		$query = static::query();
		return $this->parseWhereId($query)->delete();
	}

	/**
	 * 统计数量
	 * @param string $field
	 * @return int
	 */
	public static function count($field = '*')
	{
		return static::aggregate('count', $field);
	}

	/**
	 * 求和
	 * @param string $field
	 * @return float
	 */
	public static function sum($field)
	{
		return static::aggregate('sum', $field);
	}

	/**
	 * 平均值
	 * @param string $field
	 * @return float
	 */
	public static function avg($field)
	{
		return static::aggregate('avg', $field);
	}
	
	/**
	 * 最大值
	 * @param string $field
	 * @return float
	 */
	public static function max($field)
	{
		return static::aggregate('max', $field);
	}
	
	/**
	 * 最小值
	 * @param string $field
	 * @return float
	 */
	public static function min($field)
	{
		return static::aggregate('min', $field);
	}

	/**
	 * 聚合函数
	 * @param string $functionName
	 * @param string $fieldName
	 * @param callable $queryCallable
	 * @return mixed
	 */
	public static function aggregate($functionName, $fieldName, callable $queryCallable = null)
	{
		$query = static::query();
		if(null !== $queryCallable)
		{
			// 回调传入条件
			Call::callUserFunc($queryCallable, $query);
		}
		return Call::callUserFunc([$query, $functionName], $fieldName);
	}

	/**
	 * 处理主键where条件
	 * @param IQuery $query
	 * @return IQuery
	 */
	private function parseWhereId(IQuery $query)
	{
		// 主键条件加入
		$id = ModelManager::getId(static::class);
		if(is_string($id))
		{
			$id = [$id];
		}
		foreach($id as $idName)
		{
			if(isset($this->$idName))
			{
				$query->where($idName, '=', $this->$idName);
			}
		}
		return $query;
	}

	// 实现接口的方法们：

	public function offsetExists($offset)
	{
		$methodName = 'get' . ucfirst($offset);
		return method_exists($this, $methodName) && null !== Call::callUserFunc([$this, $methodName]);
	}

	public function offsetGet($offset)
	{
		$methodName = 'get' . ucfirst($offset);
		if(!method_exists($this, $methodName))
		{
			return null;
		}
		return Call::callUserFunc([$this, $methodName]);
	}

	public function offsetSet($offset, $value)
	{
		$methodName = 'set' . ucfirst($offset);
		if(!method_exists($this, $methodName))
		{
			return;
		}
		Call::callUserFunc([$this, $methodName], $value);
	}

	public function offsetUnset($offset)
	{
		
	}

	public function current()
	{
		return $this[current($this->__fieldNames)];
	}

	public function key()
	{
		return current($this->__fieldNames);
	}

	public function next()
	{
		next($this->__fieldNames);
	}

	public function rewind()
	{
		reset($this->__fieldNames);
	}

	public function valid()
	{
		return false !== current($this->__fieldNames);
	}

	/**
	 * 将当前对象作为数组返回
	 * @return array
	 */
	public function toArray(): array
	{
		return \iterator_to_array($this);
	}
}