<?php
namespace Imi\Model;

use Imi\Db\Db;
use Imi\Util\Text;
use Imi\Event\Event;
use Imi\Bean\BeanFactory;
use Imi\Util\LazyArrayObject;
use Imi\Model\Event\ModelEvents;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;

/**
 * 常用的数据库模型
 */
abstract class Model extends BaseModel
{
	/**
	 * 返回一个查询器
	 * @param string|object $object
	 * @return \Imi\Db\Query\Interfaces\IQuery
	 */
	public static function query($object = null)
	{
		$class = BeanFactory::getObjectClass($object ?? static::class);
		return Db::query(ModelManager::getDbPoolName($class), $class)->table(ModelManager::getTable($class));
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
	 * 
	 * @param mixed $data
	 * @return IResult
	 */
	public function insert($data = null): IResult
	{
		if(null === $data)
		{
			$data = static::parseSaveData($this);
		}
		else if(!$data instanceof \ArrayAccess)
		{
			$data = new LazyArrayObject($data);
		}
		$query = static::query($this);

		// 插入前
		$this->trigger(ModelEvents::BEFORE_INSERT, [
			'model'	=>	$this,
			'data'	=>	$data,
			'query'	=>	$query,
		], $this, \Imi\Model\Event\Param\BeforeInsertEventParam::class);

		$result = $query->insert($data);
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

		// 插入后
		$this->trigger(ModelEvents::AFTER_INSERT, [
			'model'	=>	$this,
			'data'	=>	$data,
			'result'=>	$result
		], $this, \Imi\Model\Event\Param\AfterInsertEventParam::class);

		return $result;
	}

	/**
	 * 更新记录
	 * 
	 * @param mixed $data
	 * @return IResult
	 */
	public function update($data = null): IResult
	{
		$query = static::query($this);
		$query = $this->parseWhereId($query);
		if(null === $data)
		{
			$data = static::parseSaveData($this);
		}
		else if(!$data instanceof \ArrayAccess)
		{
			$data = new LazyArrayObject($data);
		}

		// 更新前
		$this->trigger(ModelEvents::BEFORE_UPDATE, [
			'model'	=>	$this,
			'data'	=>	$data,
			'query'	=>	$query,
		], $this, \Imi\Model\Event\Param\BeforeUpdateEventParam::class);

		if(!isset($query->getOption()->where[0]))
		{
			throw new \RuntimeException('use Model->update(), primary key can not be null');
		}

		$result = $query->update($data);

		// 更新后
		$this->trigger(ModelEvents::AFTER_UPDATE, [
			'model'	=>	$this,
			'data'	=>	$data,
			'result'=>	$result,
		], $this, \Imi\Model\Event\Param\AfterUpdateEventParam::class);

		return $result;
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
		$updateData = static::parseSaveData($data);
		$query = static::parseWhere($query, $where);

		// 更新前
		Event::trigger(static::class . ModelEvents::BEFORE_BATCH_UPDATE, [
			'data'	=>	$updateData,
			'query'	=>	$query,
		], null, \Imi\Model\Event\Param\BeforeBatchUpdateEventParam::class);
		
		$result = $query->update($updateData);

		// 更新后
		Event::trigger(static::class . ModelEvents::AFTER_BATCH_UPDATE, [
			'data'	=>	$updateData,
			'result'=>	$result,
		], null, \Imi\Model\Event\Param\BeforeBatchUpdateEventParam::class);

		return $result;
	}

	/**
	 * 保存记录
	 * @return IResult
	 */
	public function save(): IResult
	{
		$query = static::query($this);
		$query = $this->parseWhereId($query);
		$data = static::parseSaveData($this);

		// 保存前
		$this->trigger(ModelEvents::BEFORE_SAVE, [
			'model'	=>	$this,
			'data'	=>	$data,
			'query'	=>	$query,
		], $this, \Imi\Model\Event\Param\BeforeSaveEventParam::class);

		if(isset($query->getOption()->where[0]))
		{
			$selectResult = $query->select();
			if($selectResult->getRowCount() > 0)
			{
				// 更新
				$result = $this->update($data);
			}
		}
		if(!isset($result))
		{
			// 插入
			$result = $this->insert($data);
		}

		// 保存后
		$this->trigger(ModelEvents::AFTER_SAVE, [
			'model'	=>	$this,
			'data'	=>	$data,
			'result'=>	$result,
		], $this, \Imi\Model\Event\Param\BeforeSaveEventParam::class);

		return $result;
	}

	/**
	 * 删除记录
	 * @return IResult
	 */
	public function delete(): IResult
	{
		$query = static::query($this);
		$query = $this->parseWhereId($query);

		// 删除前
		$this->trigger(ModelEvents::BEFORE_DELETE, [
			'model'	=>	$this,
			'query'	=>	$query,
		], $this, \Imi\Model\Event\Param\BeforeDeleteEventParam::class);

		if(!isset($query->getOption()->where[0]))
		{
			throw new \RuntimeException('use Model->delete(), primary key can not be null');
		}
		$result = $query->delete();

		// 删除后
		$this->trigger(ModelEvents::AFTER_DELETE, [
			'model'	=>	$this,
			'result'=>	$result,
		], $this, \Imi\Model\Event\Param\AfterDeleteEventParam::class);

		return $result;
	}

	/**
	 * 批量删除
	 * @param array|callable $where
	 * @return IResult
	 */
	public static function deleteBatch($where = null): IResult
	{
		$query = static::query();
		$query = static::parseWhere($query, $where);

		// 删除前
		Event::trigger(static::class . ModelEvents::BEFORE_BATCH_DELETE, [
			'query'	=>	$query,
		], null, \Imi\Model\Event\Param\BeforeBatchDeleteEventParam::class);

		$result = $query->delete();

		// 删除后
		Event::trigger(static::class . ModelEvents::AFTER_BATCH_DELETE, [
			'result'=>	$result,
		], null, \Imi\Model\Event\Param\BeforeBatchDeleteEventParam::class);

		return $result;
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
		if(null === $where)
		{
			return $query;
		}
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
					$operation = array_shift($v);
					$query->where($k, $operation, $v[0]);
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
	private static function parseSaveData($data, $object = null)
	{
		if(null === $object && is_object($data))
		{
			$object = $data;
		}
		if($data instanceof static)
		{
			$data = $data->toArray();
		}
		$class = BeanFactory::getObjectClass($object ?? static::class);
		$result = new LazyArrayObject;
		foreach(ModelManager::getFieldNames($class) as $name)
		{
			if(array_key_exists($name, $data))
			{
				$result[$name] = $data[$name];
			}
			else
			{
				$fieldName = Text::toCamelName($name);
				if(array_key_exists($fieldName, $data))
				{
					$result[$name] = $data[$fieldName];
				}
			}
		}
		return $result;
	}

}