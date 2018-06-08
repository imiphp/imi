<?php
namespace Imi\Db\Query;

use Imi\Db\Query\Order;
use Imi\RequestContext;
use Imi\Db\Interfaces\IDb;
use Imi\Bean\Annotation\Bean;
use Imi\Db\Query\Where\Where;
use Imi\Db\Query\Having\Having;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Consts\LogicalOperator;
use Imi\Db\Query\Interfaces\IField;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IHaving;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\Where\WhereBrackets;
use Imi\Db\Query\Builder\DeleteBuilder;
use Imi\Db\Query\Builder\InsertBuilder;
use Imi\Db\Query\Builder\SelectBuilder;
use Imi\Db\Query\Builder\UpdateBuilder;
use Imi\Db\Query\Having\HavingBrackets;
use Imi\Db\Query\Interfaces\IBaseWhere;

/**
 * @Bean("Query")
 */
class Query implements IQuery
{
	/**
	 * 操作记录
	 * @var QueryOption
	 */
	private $option;

	/**
	 * 数据绑定
	 * @var array
	 */
	private $binds = [];

	/**
	 * 数据库操作对象
	 * @var IDb
	 */
	private $db;

	/**
	 * 查询结果类的类名，为null则为数组
	 * @var string
	 */
	private $modelClass;

	public function __construct(IDb $db, $modelClass = null)
	{
		$this->db = $db;
		$this->modelClass = $modelClass;
	}

	public function __init()
	{
		$this->option = new QueryOption;
	}

	/**
	 * 获取所有操作的记录
	 * @return QueryOption
	 */
	public function getOption(): QueryOption
	{
		return $this->option;
	}

	/**
	 * 设置操作记录
	 * @param QueryOption $options
	 * @return static
	 */
	public function setOption(QueryOption $option)
	{
		$this->option = $option;
		return $this;
	}

	/**
	 * 获取数据库操作对象
	 * @return IDb
	 */
	public function getDb(): IDb
	{
		return $this->db;
	}

	/**
	 * 设置表名
	 * @param string $table 表名
	 * @param string $alias 别名
	 * @param string $database 数据库名
	 * @return static
	 */
	public function table(string $table, string $alias = null, string $database = null)
	{
		$this->option->table->useRaw(false);
		$this->option->table->setTable($table);
		$this->option->table->setAlias($alias);
		$this->option->table->setDatabase($database);
		return $this;
	}

	/**
	 * 设置表名，使用SQL原生语句
	 * @param string $raw
	 * @return static
	 */
	public function tableRaw(string $raw)
	{
		$this->option->table->useRaw(true);
		$this->option->table->setRawSQL($raw);
		return $this;
	}

	/**
	 * 设置表名，table()的别名
	 * @param string $table 表名
	 * @param string $alias 别名
	 * @param string $database 数据库名
	 * @return static
	 */
	public function from(string $table, string $alias = null, string $database = null)
	{
		return $this->table($table, $alias, $database);
	}

	/**
	 * 设置表名，使用SQL原生语句
	 * @param string $raw
	 * @return static
	 */
	public function fromRaw(string $raw)
	{
		return $this->fromRaw($raw);
	}

	/**
	 * 设置 distinct
	 * @param boolean $isDistinct 是否设置distinct
	 * @return static
	 */
	public function distinct($isDistinct = true)
	{
		$this->option->distinct = $isDistinct;
		return $this;
	}

	/**
	 * 指定查询字段
	 * @param string|array|IField $fields 查询字段
	 * @return static
	 */
	public function field(...$fields)
	{
		if(!isset($fields[1]) && is_array($fields[0]))
		{
			$this->option->field = array_merge($this->option->field, $fields[0]);
		}
		else
		{
			$this->option->field = array_merge($this->option->field, $fields);
		}
		return $this;
	}

	/**
	 * 指定查询字段，使用SQL原生语句
	 * @param string $raw
	 * @return static
	 */
	public function fieldRaw(string $raw)
	{
		$field = new Field();
		$field->useRaw();
		$field->setRawSQL($raw);
		$this->option->field[] = $field;
		return $this;
	}

	/**
	 * 设置 where 条件，一般用于 =、>、<、like 等
	 * @param string $fieldName
	 * @param string $operation
	 * @param mixed $value
	 * @param string $logicalOperator
	 * @return static
	 */
	public function where(string $fieldName, string $operation, $value, string $logicalOperator = LogicalOperator::AND)
	{
		$this->option->where[] = new Where($fieldName, $operation, $value, $logicalOperator);
		return $this;
	}

	/**
	 * 设置 where 条件，用原生语句
	 * @param string $raw
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereRaw(string $raw, string $logicalOperator = LogicalOperator::AND)
	{
		$where = new Where();
		$where->useRaw();
		$where->setRawSQL($raw);
		$where->setLogicalOperator($logicalOperator);
		$this->option->where[] = $where;
		return $this;
	}

	/**
	 * 设置 where 条件，传入回调，回调中的条件加括号
	 * @param callable $callback
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereBrackets(callable $callback, string $logicalOperator = LogicalOperator::AND)
	{
		$this->option->where[] = new WhereBrackets($callback, $logicalOperator);
		return $this;
	}

	/**
	 * 设置 where 条件，使用 IBaseWhere 结构
	 * @param IBaseWhere $where
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereStruct(IBaseWhere $where, string $logicalOperator = LogicalOperator::AND)
	{
		$this->option->where[] = $where;
		return $this;
	}

	/**
	 * where between $begin end $end
	 * @param string $fieldName
	 * @param mixed $begin
	 * @param mixed $end
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereBetween(string $fieldName, $begin, $end, string $logicalOperator = LogicalOperator::AND)
	{
		return $this->where($fieldName, 'between', [$begin, $end], $logicalOperator);
	}

	/**
	 * or where between $begin end $end
	 * @param string $fieldName
	 * @param mixed $begin
	 * @param mixed $end
	 * @return static
	 */
	public function orWhereBetween(string $fieldName, $begin, $end)
	{
		return $this->where($fieldName, 'between', [$begin, $end], LogicalOperator::OR);
	}

	/**
	 * where not between $begin end $end
	 * @param string $fieldName
	 * @param mixed $begin
	 * @param mixed $end
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereNotBetween(string $fieldName, $begin, $end, string $logicalOperator = LogicalOperator::AND)
	{
		return $this->where($fieldName, 'not between', [$begin, $end], $logicalOperator);
	}

	/**
	 * or where not between $begin end $end
	 * @param string $fieldName
	 * @param mixed $begin
	 * @param mixed $end
	 * @return static
	 */
	public function orWhereNotBetween(string $fieldName, $begin, $end)
	{
		return $this->where($fieldName, 'not between', [$begin, $end], LogicalOperator::OR);
	}

	/**
	 * 设置 where or 条件
	 * @param string $fieldName
	 * @param string $operation
	 * @param mixed $value
	 * @return static
	 */
	public function orWhere(string $fieldName, string $operation, $value)
	{
		return $this->where($fieldName, $operation, $value, LogicalOperator::OR);
	}

	/**
	 * 设置 where or 条件，用原生语句
	 * @param string $where
	 * @return static
	 */
	public function orWhereRaw(string $where)
	{
		return $this->whereRaw($where, LogicalOperator::OR);
	}

	/**
	 * 设置 where or 条件，传入回调，回调中的条件加括号
	 * @param callable $callback
	 * @return static
	 */
	public function orWhereBrackets(callable $callback)
	{
		return $this->whereBrackets($callback, LogicalOperator::OR);
	}

	/**
	 * 设置 where or 条件，使用 IBaseWhere 结构
	 * @param IBaseWhere $where
	 * @return static
	 */
	public function orWhereStruct(IBaseWhere $where)
	{
		return $this->whereStruct($where, LogicalOperator::OR);
	}

	/**
	 * where field in (list)
	 * @param string $fieldName
	 * @param array $list
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereIn(string $fieldName, $list, string $logicalOperator = LogicalOperator::AND)
	{
		return $this->where($fieldName, 'in', $list, $logicalOperator);
	}

	/**
	 * or where field in (list)
	 * @param string $fieldName
	 * @param array $list
	 * @return static
	 */
	public function orWhereIn(string $fieldName, $list)
	{
		return $this->where($fieldName, 'in', $list, LogicalOperator::OR);
	}

	/**
	 * where field not in (list)
	 * @param string $fieldName
	 * @param array $list
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereNotIn(string $fieldName, $list, string $logicalOperator = LogicalOperator::AND)
	{
		return $this->where($fieldName, 'not in', $list, $logicalOperator);
	}

	/**
	 * or where field not in (list)
	 * @param string $fieldName
	 * @param array $list
	 * @return static
	 */
	public function orWhereNotIn(string $fieldName, $list)
	{
		return $this->where($fieldName, 'not in', $list, LogicalOperator::OR);
	}

	/**
	 * where field is null
	 * @param string $fieldName
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereIsNull(string $fieldName, string $logicalOperator = LogicalOperator::AND)
	{
		return $this->where($fieldName, 'is', null, $logicalOperator);
	}

	/**
	 * or where field is null
	 * @param string $fieldName
	 * @return static
	 */
	public function orWhereIsNull(string $fieldName)
	{
		return $this->where($fieldName, 'is', null, LogicalOperator::OR);
	}

	/**
	 * where field is not null
	 * @param string $fieldName
	 * @param string $logicalOperator
	 * @return static
	 */
	public function whereIsNotNull(string $fieldName, string $logicalOperator = LogicalOperator::AND)
	{
		return $this->where($fieldName, 'is not', null, $logicalOperator);
	}

	/**
	 * or where field is not null
	 * @param string $fieldName
	 * @return static
	 */
	public function orWhereIsNotNull(string $fieldName)
	{
		return $this->where($fieldName, 'is not', null, LogicalOperator::OR);
	}

	/**
	 * join
	 * @param string $table 表名
	 * @param string $left 在 join b on a.id=b.id 中的 a.id
	 * @param string $operation 在 join b on a.id=b.id 中的 =
	 * @param string $right 在 join b on a.id=b.id 中的 b.id
	 * @param string $tableAlias 表别名
	 * @param IBaseWhere $where where条件
	 * @param string $type join类型，默认left
	 * @return static
	 */
	public function join(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null, string $type = 'left')
	{
		$this->option->join[] = new Join($table, $left, $operation, $right, $tableAlias, $where, $type);
		return $this;
	}

	/**
	 * join，使用SQL原生语句
	 * @param string $raw
	 * @return static
	 */
	public function joinRaw(string $raw)
	{
		$join = new Join();
		$join->useRaw();
		$join->setRawSQL($raw);
		$this->option->join[] = $join;
		return $this;
	}

	/**
	 * left join
	 * @param string $table 表名
	 * @param string $left 在 join b on a.id=b.id 中的 a.id
	 * @param string $operation 在 join b on a.id=b.id 中的 =
	 * @param string $right 在 join b on a.id=b.id 中的 b.id
	 * @param string $tableAlias 表别名
	 * @param IBaseWhere $where where条件
	 * @return static
	 */
	public function leftJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null)
	{
		return $this->join($table, $left, $operation, $right, $tableAlias, $where);
	}

	/**
	 * right join
	 * @param string $table 表名
	 * @param string $left 在 join b on a.id=b.id 中的 a.id
	 * @param string $operation 在 join b on a.id=b.id 中的 =
	 * @param string $right 在 join b on a.id=b.id 中的 b.id
	 * @param string $tableAlias 表别名
	 * @param IBaseWhere $where where条件
	 * @return static
	 */
	public function rightJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null)
	{
		return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'right');
	}

	/**
	 * cross join
	 * @param string $table 表名
	 * @param string $left 在 join b on a.id=b.id 中的 a.id
	 * @param string $operation 在 join b on a.id=b.id 中的 =
	 * @param string $right 在 join b on a.id=b.id 中的 b.id
	 * @param string $tableAlias 表别名
	 * @param IBaseWhere $where where条件
	 * @return static
	 */
	public function crossJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null)
	{
		return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'cross');
	}

	/**
	 * 排序
	 * @param string $field
	 * @param string $direction
	 * @return static
	 */
	public function order(string $field, string $direction = 'asc')
	{
		$this->option->order[] = new Order($field, $direction);
		return $this;
	}

	/**
	 * 排序
	 * 支持的写法：
	 * 1. id desc, age asc
	 * 2. ['id'=>'desc', 'age'] // 缺省asc
	 * @param string|array $raw
	 * @return static
	 */
	public function orderRaw($raw)
	{
		if(is_array($raw))
		{
			foreach($raw as $k => $v)
			{
				if(is_numeric($k))
				{
					$fieldName = $v;
					$direction = 'asc';
				}
				else
				{
					$fieldName = $k;
					$direction = $v;
				}
				$this->option->order[] = new Order($fieldName, $direction);
			}
		}
		else
		{
			$order = new Order();
			$order->useRaw();
			$order->setRawSQL($raw);
			$this->option->order[] = $order;
		}
		return $this;
	}

	/**
	 * 设置分页
	 * 传入当前页码和每页显示数量，自动计算offset和limit
	 * @param int $page
	 * @param int $show
	 * @return static
	 */
	public function page($page, $show)
	{
		$this->option->offset = max((int)(($page - 1) * $show), 0);
		$this->option->limit = $show;
		return $this;
	}

	/**
	 * 设置记录从第几个开始取出
	 * @param int $offset
	 * @return static
	 */
	public function offset($offset)
	{
		$this->option->offset = $offset;
		return $this;
	}
	
	/**
	 * 设置查询几条记录
	 * @param int $offset
	 * @return static
	 */
	public function limit($limit)
	{
		$this->option->limit = $limit;
		return $this;
	}

	/**
	 * group by
	 * @param string ...$groups
	 * @return static
	 */
	public function group(...$groups)
	{
		foreach($groups as $item)
		{
			$group = new Group();
			$group->setValue($item);
			$this->option->group[] = $group;
		}
		return $this;
	}

	/**
	 * group by，使用SQL原生语句
	 * @param string $raw
	 * @return static
	 */
	public function groupRaw(string $raw)
	{
		$group = new Group();
		$group->useRaw();
		$group->setRawSQL($raw);
		$this->option->group[] = $group;
		return $this;
	}

	/**
	 * 设置 having 条件
	 * @param string $fieldName
	 * @param string $operation
	 * @param mixed $value
	 * @param string $logicalOperator
	 * @return static
	 */
	public function having(string $fieldName, string $operation, $value, string $logicalOperator = LogicalOperator::AND)
	{
		$this->option->having[] = new Having($fieldName, $operation, $value, $logicalOperator);
		return $this;
	}

	/**
	 * 设置 having 条件，用原生语句
	 * @param string $raw
	 * @param string $logicalOperator
	 * @return static
	 */
	public function havingRaw(string $raw, string $logicalOperator = LogicalOperator::AND)
	{
		$having = new Having();
		$having->useRaw();
		$having->setRawSQL($raw);
		$having->setLogicalOperator($logicalOperator);
		$this->option->having[] = $having;
		return $this;
	}

	/**
	 * 设置 having 条件，传入回调，回调中的条件加括号
	 * @param callable $callback
	 * @param string $logicalOperator
	 * @return static
	 */
	public function havingBrackets(callable $callback, string $logicalOperator = LogicalOperator::AND)
	{
		$this->option->having[] = new HavingBrackets($callback, $logicalOperator);
		return $this;
	}

	/**
	 * 设置 having 条件，使用 IHaving 结构
	 * @param IHaving $having
	 * @param string $logicalOperator
	 * @return static
	 */
	public function havingStruct(IHaving $having, string $logicalOperator = LogicalOperator::AND)
	{
		$this->option->having[] = $having;
		return $this;
	}

	/**
	 * 绑定预处理参数
	 * @param string|int $name
	 * @param mixed $value
	 * @param int $dataType
	 * @return static
	 */
	public function bindValue($name, $value, $dataType = \PDO::PARAM_STR)
	{
		$this->binds[$name] = $value;
		return $this;
	}

	/**
	 * 批量绑定预处理参数
	 * @param array $values
	 * @return static
	 */
	public function bindValues($values)
	{
		foreach($values as $k => $v)
		{
			$this->binds[$k] = $v;
		}
		return $this;
	}

	/**
	 * 获取绑定预处理参数关系
	 * @return array
	 */
	public function getBinds()
	{
		return $this->binds;
	}

	/**
	 * 查询记录
	 * @return IResult
	 */
	public function select(): IResult
	{
		$builder = new SelectBuilder($this);
		$sql = $builder->build();
		$result = new Result($this->execute($sql), $this->modelClass);
		$this->__init();
		return $result;
	}

	/**
	 * 更新数据
	 * @param array $data
	 * @return IResult
	 */
	public function insert($data): IResult
	{
		$builder = new InsertBuilder($this);
		$sql = $builder->build($data);
		$result = new Result($this->execute($sql), $this->modelClass);
		$this->__init();
		return $result;
	}

	/**
	 * 更新数据
	 * @param array $data
	 * @return IResult
	 */
	public function update($data): IResult
	{
		$builder = new UpdateBuilder($this);
		$sql = $builder->build($data);
		$result = new Result($this->execute($sql), $this->modelClass);
		$this->__init();
		return $result;
	}

	/**
	 * 删除数据
	 * @return IResult
	 */
	public function delete(): IResult
	{
		$builder = new DeleteBuilder($this);
		$sql = $builder->build();
		$result = new Result($this->execute($sql), $this->modelClass);
		$this->__init();
		return $result;
	}

	/**
	 * 执行SQL语句
	 * @param string $sql
	 * @return IStatement|bool
	 */
	protected function execute($sql)
	{
		if(empty($this->binds))
		{
			$result = $this->db->query($sql);
		}
		else
		{
			$stmt = $this->db->prepare($sql);
			if($stmt)
			{
				$result = $stmt->execute($this->binds) ? $stmt : false;
			}
			else
			{
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * 获取自动起名的参数名称
	 * @return string
	 */
	public static function getAutoParamName()
	{
		$index = RequestContext::get('dbParamInc', 0);
		if($index >= 65535) // 限制dechex()结果最长为ffff，一般一个查询也不会用到这么多参数，足够了
		{
			$index = 0;
		}
		++$index;
		RequestContext::set('dbParamInc', $index);
		return ':p' . dechex($index);
	}
}