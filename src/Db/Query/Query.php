<?php
namespace Imi\Db\Query;

use Imi\Db\Db;
use Imi\Db\Query\Order;
use Imi\RequestContext;
use Imi\Util\Pagination;
use Imi\Pool\PoolManager;
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
use Imi\Db\Query\Builder\ReplaceBuilder;
use Imi\Db\Query\Builder\BatchInsertBuilder;
use Imi\Db\Query\Interfaces\IPaginateResult;

/**
 * @Bean("Query")
 */
class Query implements IQuery
{
    /**
     * 操作记录
     * @var QueryOption
     */
    protected $option;

    /**
     * 数据绑定
     * @var array
     */
    protected $binds = [];

    /**
     * 数据库操作对象
     * @var IDb
     */
    protected $db;

    /**
     * 连接池名称
     *
     * @var string
     */
    protected $poolName;

    /**
     * 查询结果类的类名，为null则为数组
     * @var string
     */
    protected $modelClass;

    /**
     * 查询类型
     *
     * @var int
     */
    protected $queryType;

    /**
     * 是否初始化时候就设定了查询类型
     *
     * @var boolean
     */
    protected $isInitQueryType;

    /**
     * 是否初始化时候就设定了连接
     *
     * @var boolean
     */
    protected $isInitDb;

    /**
     * 数据库字段自增
     *
     * @var integer
     */
    protected $dbParamInc = 0;

    public function __construct(IDb $db = null, $modelClass = null, $poolName = null, $queryType = null)
    {
        $this->db = $db;
        $this->isInitDb = null !== $this->db;
        $this->poolName = $poolName;
        $this->modelClass = $modelClass;
        $this->queryType = $queryType;
        $this->isInitQueryType = null !== $queryType;
    }

    public function __init()
    {
        $this->dbParamInc = 0;
        $this->option = new QueryOption;
        if(!$this->isInitQueryType)
        {
            $this->queryType = null;
        }
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
        $this->dbParamInc = 0;
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
     * 设置 where 条件，支持语法如下：
     * 
     * [
     *      'id'	=>	1,
     *      'or'	=>	[
     *          'id'	=>	2,
     *      ],
     *      'title'	    =>	['like', '%test%'],
     *      'age'	    =>	['>', 18],
     *      'age'  =>  ['between', 19, 29]
     * ]
     * 
     * SQL: id = 1 or (id = 2) and title like '%test%' and age > 18 and age between 19 and 29
     *
     * @param array $condition
     * @param string $logicalOperator
     * @return static
     */
    public function whereEx(array $condition, string $logicalOperator = LogicalOperator::AND)
    {
        if(!$condition){
            return $this;
        }
        $func = function($condition) use(&$func){
            $result = [];
            foreach($condition as $key => $value)
            {
                if(null === LogicalOperator::getText(strtolower($key)))
                {
                    // 条件 k => v
                    if(is_array($value))
                    {
                        $operator = strtolower($value[0] ?? '');
                        switch($operator)
                        {
                            case 'between':
                                if(!isset($value[2]))
                                {
                                    throw new \RuntimeException('Between must have 3 params');
                                }
                                $result[] = new Where($key, 'between', [$value[1], $value[2]]);
                                break;
                            case 'not between':
                                if(!isset($value[2]))
                                {
                                    throw new \RuntimeException('Not between must have 3 params');
                                }
                                $result[] = new Where($key, 'not between', [$value[1], $value[2]]);
                                break;
                            case 'in':
                                if(!isset($value[1]))
                                {
                                    throw new \RuntimeException('In must have 3 params');
                                }
                                $result[] = new Where($key, 'in', $value[1]);
                                break;
                            case 'not in':
                                if(!isset($value[1]))
                                {
                                    throw new \RuntimeException('Not in must have 3 params');
                                }
                                $result[] = new Where($key, 'not in', $value[1]);
                                break;
                            default:
                                $result[] = new Where($key, $operator, $value[1]);
                                break;
                        }
                    }
                    else
                    {
                        $result[] = new Where($key, '=', $value);
                    }
                }
                else
                {
                    // 逻辑运算符
                    $result[] = new WhereBrackets(function() use($func, $value){
                        return $func($value);
                    }, $key);
                }
            }
            return $result;
        };
        return $this->whereBrackets(function() use($condition, $func){
            return $func($condition);
        }, $logicalOperator);
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
     * 设置 where or 条件，支持语法参考 whereEx 方法
     *
     * @param array $condition
     * @return static
     */
    public function orWhereEx(array $condition)
    {
        return $this->whereEx($condition, LogicalOperator::OR);
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
     * @param string $type join类型，默认inner
     * @return static
     */
    public function join(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null, string $type = 'inner')
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
        return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'left');
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
     * @param int $count
     * @return static
     */
    public function page($page, $count)
    {
        $pagination = new Pagination($page, $count);
        $this->option->offset = $pagination->getLimitOffset();
        $this->option->limit = $count;
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
        if(!$this->isInitQueryType && !$this->isInTransaction())
        {
            $this->queryType = QueryType::READ;
        }
        return $this->execute($sql);
    }

    /**
     * 分页查询
     *
     * @param boolean $status 设置为true时，查询结果会返回为分页格式
     * @param array $options
     * @return \Imi\Db\Query\Interfaces\IPaginateResult
     */
    public function paginate($page, $count, $options = []): IPaginateResult
    {
        if($options['total'] ?? true)
        {
            $option = clone $this->option;
            $queryType = $this->queryType;
            $total = (int)$this->count();
            $this->option = $option;
            $this->queryType = $queryType;
        }
        else
        {
            $total = null;
        }
        $this->page($page, $count);
        $statement = $this->select();
        $pagination = new Pagination($page, $count);
        return new PaginateResult($statement, $pagination->getLimitOffset(), $count, $total, null === $total ? null : $pagination->calcPageCount($total), $options);
    }

    /**
     * 插入记录
     * @param array $data
     * @return IResult
     */
    public function insert($data = null): IResult
    {
        $builder = new InsertBuilder($this);
        $sql = $builder->build($data);
        return $this->execute($sql);
    }

    
    /**
     * 批量插入数据
     * 以第 0 个成员作为字段标准
     *
     * @param array $data
     * @return IResult
     */
    public function batchInsert($data = null): IResult
    {
        $builder = new BatchInsertBuilder($this);
        $sql = $builder->build($data);
        return $this->execute($sql);
    }

    /**
     * 更新记录
     * @param array $data
     * @return IResult
     */
    public function update($data = null): IResult
    {
        $builder = new UpdateBuilder($this);
        $sql = $builder->build($data);
        return $this->execute($sql);
    }

    /**
     * 替换数据（Replace）
     *
     * @param array $data
     * @return IResult
     */
    public function replace($data = null): IResult
    {
        $builder = new ReplaceBuilder($this);
        $sql = $builder->build($data);
        return $this->execute($sql);
    }

    /**
     * 删除记录
     * @return IResult
     */
    public function delete(): IResult
    {
        $builder = new DeleteBuilder($this);
        $sql = $builder->build();
        $result = $this->execute($sql);
        $this->__init();
        return $result;
    }

    /**
     * 统计数量
     * @param string $field
     * @return int
     */
    public function count($field = '*')
    {
        return $this->aggregate('count', $field);
    }

    /**
     * 求和
     * @param string $field
     * @return float
     */
    public function sum($field)
    {
        return $this->aggregate('sum', $field);
    }

    /**
     * 平均值
     * @param string $field
     * @return float
     */
    public function avg($field)
    {
        return $this->aggregate('avg', $field);
    }
    
    /**
     * 最大值
     * @param string $field
     * @return float
     */
    public function max($field)
    {
        return $this->aggregate('max', $field);
    }
    
    /**
     * 最小值
     * @param string $field
     * @return float
     */
    public function min($field)
    {
        return $this->aggregate('min', $field);
    }

    /**
     * 聚合函数
     * @param string $functionName
     * @param string $fieldName
     * @return mixed
     */
    public function aggregate($functionName, $fieldName)
    {
        $field = new Field;
        $field->useRaw();
        $field->setRawSQL($functionName . '(' . $field->parseKeyword($fieldName). ')');
        $this->option->field = [
            $field
        ];
        return $this->select()->getScalar();
    }

    /**
     * 执行SQL语句
     * @param string $sql
     * @return IResult
     */
    public function execute($sql)
    {
        try{
            if(null === $this->queryType)
            {
                $this->queryType = QueryType::WRITE;
            }
            if(!$this->isInitDb)
            {
                $this->db = Db::getInstance($this->poolName, $this->queryType);
            }
            if(!$this->db)
            {
                return new Result(false);
            }
            $stmt = $this->db->prepare($sql);
            if($stmt)
            {
                $binds = $this->binds;
                $this->binds = [];
                $stmt->execute($binds);
            }
            return new Result($stmt, $this->modelClass);
        } finally {
            $this->__init();
        }
    }

    /**
     * 获取自动起名的参数名称
     * @return string
     */
    public function getAutoParamName()
    {
        if($this->dbParamInc >= 65535) // 限制dechex()结果最长为ffff，一般一个查询也不会用到这么多参数，足够了
        {
            $this->dbParamInc = 0;
        }
        ++$this->dbParamInc;
        return ':p' . dechex($this->dbParamInc);
    }

    /**
     * 设置update/insert/replace数据
     * 
     * @param array|\Imi\Db\Query\Raw[]|\Imi\Db\Query\Interfaces\IQuery $data
     * @return static
     */
    public function setData($data)
    {
        $this->option->saveData = $data;
        return $this;
    }

    /**
     * 设置update/insert/replace的字段
     *
     * @param string $fieldName
     * @param mixed $value
     * @return static
     */
    public function setField($fieldName, $value)
    {
        $this->option->saveData[$fieldName] = $value;
        return $this;
    }

    /**
     * 设置update/insert/replace的字段，值为表达式，原样代入
     *
     * @param string $fieldName
     * @param string $exp
     * @return static
     */
    public function setFieldExp($fieldName, $exp)
    {
        $this->option->saveData[$fieldName] = new Raw($exp);
        return $this;
    }

    /**
     * 设置递增字段
     *
     * @param string $fieldName
     * @param float $incValue
     * @return static
     */
    public function setFieldInc($fieldName, float $incValue = 1)
    {
        $this->option->saveData[$fieldName] = new Raw(new Field($fieldName) . ' + ' . $incValue);
        return $this;
    }

    /**
     * 设置递减字段
     *
     * @param string $fieldName
     * @param float $decValue
     * @return static
     */
    public function setFieldDec($fieldName, float $decValue = 1)
    {
        $this->option->saveData[$fieldName] = new Raw(new Field($fieldName) . ' - ' . $decValue);
        return $this;
    }

    /**
     * 当前主库连接是否在事务中
     *
     * @return boolean
     */
    private function isInTransaction()
    {
        $poolName = $this->poolName;
        if(null === $poolName)
        {
            $poolName = Db::getDefaultPoolName();
        }
        if(PoolManager::hasRequestContextResource($poolName))
        {
            $resource = PoolManager::getRequestContextResource($poolName);
            $db = $resource->getInstance();
            return $db->inTransaction();
        }
        else
        {
            return false;
        }
    }
}