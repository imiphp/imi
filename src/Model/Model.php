<?php
namespace Imi\Model;

use Imi\Db\Db;
use Imi\Bean\BeanFactory;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;

/**
 * 常用的数据库模型
 */
abstract class Model extends BaseModel
{
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
			call_user_func($ids[0], $query);
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
	 * @param array|callable $where
	 * @return static[]
	 */
	public static function select($where = null)
	{
		$query = static::query();
		return static::parseWhere($query, $where)->select()->getArray();
	}

	/**
	 * 插入记录
	 * @return IResult
	 */
	public function insert(): IResult
	{
		$result = static::query()->insert(static::parseSaveData($this));
		if($result->isSuccess())
		{
			foreach(ModelManager::getFields($this) as $name => $column)
			{
				if($column->isAutoIncrement)
				{
					$this[$name] = $result->getLastInsertId();
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * 更新记录
	 * @return IResult
	 */
	public function update(): IResult
	{
		$query = static::query();
		return $this->parseWhereId($query)->update(static::parseSaveData($this));
	}

	/**
	 * 批量更新
	 * @param mixed $data
	 * @param array|callable $where
	 * @return IResult
	 */
	public static function updateBatch($data, $where = null): IResult
	{
		$query = static::query();
		$updateData = [];
		foreach($data as $item)
		{
			$updateData[] = static::parseSaveData($item);
		}
		return static::parseWhere($query, $where)->update($updateData);
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
	 * 批量删除
	 * @param array|callable $where
	 * @return IResult
	 */
	public static function deleteBatch($where = null): IResult
	{
		$query = static::query();
		return static::parseWhere($query, $where)->delete();
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
			call_user_func($queryCallable, $query);
		}
		return call_user_func([$query, $functionName], $fieldName);
	}

	/**
	 * 处理主键where条件
	 * @param IQuery $query
	 * @return IQuery
	 */
	private function parseWhereId(IQuery $query)
	{
		// 主键条件加入
		$id = ModelManager::getId($this);
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

	/**
	 * 处理where条件
	 * @param IQuery $query
	 * @param array $where
	 * @return IQuery
	 */
	private static function parseWhere(IQuery $query, $where)
	{
		if(is_callable($where))
		{
			// 回调传入条件
			call_user_func($where, $query);
		}
		else
		{
			foreach($where as $k => $v)
			{
				if(is_array($v))
				{
					$operation = array_unshift($v);
					$query->where($k, $operation, $v[1]);
				}
				else
				{
					$query->where($k, '=', $v);
				}
			}
		}
		return $query;
	}

	/**
	 * 处理保存的数据
	 * @param array $data
	 * @return array
	 */
	private static function parseSaveData($data)
	{
		$result = [];
		foreach(ModelManager::getFieldNames(static::class) as $name)
		{
			$result[$name] = $data[$name];
		}
		return $result;
	}

}