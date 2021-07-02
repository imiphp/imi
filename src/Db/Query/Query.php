<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Bean\Annotation\Bean;
use Imi\Db\Consts\LogicalOperator;
use Imi\Db\Db;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Query\Builder\BatchInsertBuilder;
use Imi\Db\Query\Builder\DeleteBuilder;
use Imi\Db\Query\Builder\InsertBuilder;
use Imi\Db\Query\Builder\ReplaceBuilder;
use Imi\Db\Query\Builder\SelectBuilder;
use Imi\Db\Query\Builder\UpdateBuilder;
use Imi\Db\Query\Having\Having;
use Imi\Db\Query\Having\HavingBrackets;
use Imi\Db\Query\Interfaces\IBaseWhere;
use Imi\Db\Query\Interfaces\IField;
use Imi\Db\Query\Interfaces\IHaving;
use Imi\Db\Query\Interfaces\IPaginateResult;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\Where\Where;
use Imi\Db\Query\Where\WhereBrackets;
use Imi\Util\Pagination;

/**
 * @Bean("Query")
 */
class Query implements IQuery
{
    /**
     * 操作记录.
     */
    protected QueryOption $option;

    /**
     * 数据绑定.
     */
    protected array $binds = [];

    /**
     * 数据库操作对象
     */
    protected ?IDb $db;

    /**
     * 连接池名称.
     */
    protected ?string $poolName = null;

    /**
     * 查询结果类的类名，为null则为数组.
     */
    protected ?string $modelClass = null;

    /**
     * 查询类型.
     */
    protected ?int $queryType = null;

    /**
     * 是否初始化时候就设定了查询类型.
     */
    protected bool $isInitQueryType = false;

    /**
     * 是否初始化时候就设定了连接.
     */
    protected bool $isInitDb = false;

    /**
     * 数据库字段自增.
     */
    protected int $dbParamInc = 0;

    /**
     * 查询器别名集合.
     *
     * @var static[]
     */
    protected static array $aliasMap = [];

    /**
     * 当前别名.
     */
    protected ?string $alias = null;

    /**
     * 查询结果集类名.
     *
     * @var string
     */
    protected $resultClass = Result::class;

    /**
     * 别名 Sql 数据集合.
     *
     * @var array
     */
    protected static array $aliasSqlMap = [];

    public function __construct(?IDb $db = null, ?string $modelClass = null, ?string $poolName = null, ?int $queryType = null)
    {
        $this->db = $db;
        $this->isInitDb = null !== $db;
        $this->poolName = $poolName;
        $this->modelClass = $modelClass;
        $this->queryType = $queryType;
        $this->isInitQueryType = null !== $queryType;
    }

    public function __init(): void
    {
        $this->dbParamInc = 0;
        $this->option = new QueryOption();
        if (!$this->isInitQueryType)
        {
            $this->queryType = null;
        }
    }

    public function __clone()
    {
        $this->isInitDb = false;
        $this->db = null;
        $this->option = clone $this->option;
    }

    /**
     * 获取所有操作的记录.
     */
    public function getOption(): QueryOption
    {
        return $this->option;
    }

    /**
     * 设置操作记录.
     *
     * @return static
     */
    public function setOption(QueryOption $option): self
    {
        $this->dbParamInc = 0;
        $this->option = $option;

        return $this;
    }

    /**
     * 获取数据库操作对象
     */
    public function getDb(): IDb
    {
        return $this->db;
    }

    /**
     * 设置表名.
     *
     * @param string      $table    表名
     * @param string|null $alias    别名
     * @param string|null $database 数据库名
     *
     * @return static
     */
    public function table(string $table, string $alias = null, string $database = null): self
    {
        $optionTable = $this->option->table;
        $optionTable->useRaw(false);
        $optionTable->setTable($table);
        $optionTable->setAlias($alias);
        $optionTable->setDatabase($database);

        return $this;
    }

    /**
     * 设置表名，使用SQL原生语句.
     *
     * @return static
     */
    public function tableRaw(string $raw, ?string $alias = null): self
    {
        $optionTable = $this->option->table;
        $optionTable->useRaw(true);
        $optionTable->setRawSQL($raw);
        $optionTable->setAlias($alias);

        return $this;
    }

    /**
     * 设置表名，table()的别名.
     *
     * @param string      $table    表名
     * @param string      $alias    别名
     * @param string|null $database 数据库名
     *
     * @return static
     */
    public function from(string $table, string $alias = null, string $database = null): self
    {
        return $this->table($table, $alias, $database);
    }

    /**
     * 设置表名，使用SQL原生语句.
     *
     * @return static
     */
    public function fromRaw(string $raw): self
    {
        return $this->fromRaw($raw);
    }

    /**
     * 设置 distinct.
     *
     * @param bool $isDistinct 是否设置distinct
     *
     * @return static
     */
    public function distinct(bool $isDistinct = true): self
    {
        $this->option->distinct = $isDistinct;

        return $this;
    }

    /**
     * 指定查询字段.
     *
     * @param string|array|IField $fields 查询字段
     *
     * @return static
     */
    public function field(...$fields): self
    {
        $option = $this->option;
        if (!isset($fields[1]) && \is_array($fields[0]))
        {
            $option->field = array_merge($option->field, $fields[0]);
        }
        else
        {
            $option->field = array_merge($option->field, $fields);
        }

        return $this;
    }

    /**
     * 指定查询字段，使用SQL原生语句.
     *
     * @return static
     */
    public function fieldRaw(string $raw, ?string $alias = null): self
    {
        $field = new Field();
        $field->useRaw();
        $field->setRawSQL($raw);
        if (null !== $alias)
        {
            $field->setAlias($alias);
        }
        $this->option->field[] = $field;

        return $this;
    }

    /**
     * 设置 where 条件，一般用于 =、>、<、like 等.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function where(string $fieldName, string $operation, $value, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->where[] = new Where($fieldName, $operation, $value, $logicalOperator);

        return $this;
    }

    /**
     * 设置 where 条件，用原生语句.
     *
     * @return static
     */
    public function whereRaw(string $raw, string $logicalOperator = LogicalOperator::AND): self
    {
        $where = new Where();
        $where->useRaw();
        $where->setRawSQL($raw);
        $where->setLogicalOperator($logicalOperator);
        $this->option->where[] = $where;

        return $this;
    }

    /**
     * 设置 where 条件，传入回调，回调中的条件加括号.
     *
     * @return static
     */
    public function whereBrackets(callable $callback, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->where[] = new WhereBrackets($callback, $logicalOperator);

        return $this;
    }

    /**
     * 设置 where 条件，使用 IBaseWhere 结构.
     *
     * @return static
     */
    public function whereStruct(IBaseWhere $where, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->where[] = $where;

        return $this;
    }

    /**
     * 设置 where 条件，支持语法如下：.
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
     * @return static
     */
    public function whereEx(array $condition, string $logicalOperator = LogicalOperator::AND): self
    {
        if (!$condition)
        {
            return $this;
        }
        $func = function (array $condition) use (&$func): array {
            $result = [];
            foreach ($condition as $key => $value)
            {
                if (null === LogicalOperator::getText(strtolower($key)))
                {
                    // 条件 k => v
                    if (\is_array($value))
                    {
                        $operator = strtolower($value[0] ?? '');
                        switch ($operator)
                        {
                            case 'between':
                                if (!isset($value[2]))
                                {
                                    throw new \RuntimeException('Between must have 3 params');
                                }
                                $result[] = new Where($key, 'between', [$value[1], $value[2]]);
                                break;
                            case 'not between':
                                if (!isset($value[2]))
                                {
                                    throw new \RuntimeException('Not between must have 3 params');
                                }
                                $result[] = new Where($key, 'not between', [$value[1], $value[2]]);
                                break;
                            case 'in':
                                if (!isset($value[1]))
                                {
                                    throw new \RuntimeException('In must have 3 params');
                                }
                                $result[] = new Where($key, 'in', $value[1]);
                                break;
                            case 'not in':
                                if (!isset($value[1]))
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
                    $result[] = new WhereBrackets(function () use ($func, $value) {
                        return $func($value);
                    }, $key);
                }
            }

            return $result;
        };

        return $this->whereBrackets(function () use ($condition, $func) {
            return $func($condition);
        }, $logicalOperator);
    }

    /**
     * where between $begin end $end.
     *
     * @param mixed $begin
     * @param mixed $end
     *
     * @return static
     */
    public function whereBetween(string $fieldName, $begin, $end, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->where($fieldName, 'between', [$begin, $end], $logicalOperator);
    }

    /**
     * or where between $begin end $end.
     *
     * @param mixed $begin
     * @param mixed $end
     *
     * @return static
     */
    public function orWhereBetween(string $fieldName, $begin, $end): self
    {
        return $this->where($fieldName, 'between', [$begin, $end], LogicalOperator::OR);
    }

    /**
     * where not between $begin end $end.
     *
     * @param mixed $begin
     * @param mixed $end
     *
     * @return static
     */
    public function whereNotBetween(string $fieldName, $begin, $end, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->where($fieldName, 'not between', [$begin, $end], $logicalOperator);
    }

    /**
     * or where not between $begin end $end.
     *
     * @param mixed $begin
     * @param mixed $end
     *
     * @return static
     */
    public function orWhereNotBetween(string $fieldName, $begin, $end): self
    {
        return $this->where($fieldName, 'not between', [$begin, $end], LogicalOperator::OR);
    }

    /**
     * 设置 where or 条件.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function orWhere(string $fieldName, string $operation, $value): self
    {
        return $this->where($fieldName, $operation, $value, LogicalOperator::OR);
    }

    /**
     * 设置 where or 条件，用原生语句.
     *
     * @return static
     */
    public function orWhereRaw(string $where): self
    {
        return $this->whereRaw($where, LogicalOperator::OR);
    }

    /**
     * 设置 where or 条件，传入回调，回调中的条件加括号.
     *
     * @return static
     */
    public function orWhereBrackets(callable $callback): self
    {
        return $this->whereBrackets($callback, LogicalOperator::OR);
    }

    /**
     * 设置 where or 条件，使用 IBaseWhere 结构.
     *
     * @return static
     */
    public function orWhereStruct(IBaseWhere $where): self
    {
        return $this->whereStruct($where, LogicalOperator::OR);
    }

    /**
     * 设置 where or 条件，支持语法参考 whereEx 方法.
     *
     * @return static
     */
    public function orWhereEx(array $condition): self
    {
        return $this->whereEx($condition, LogicalOperator::OR);
    }

    /**
     * where field in (list).
     *
     * @return static
     */
    public function whereIn(string $fieldName, array $list, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->where($fieldName, 'in', $list, $logicalOperator);
    }

    /**
     * or where field in (list).
     *
     * @return static
     */
    public function orWhereIn(string $fieldName, array $list): self
    {
        return $this->where($fieldName, 'in', $list, LogicalOperator::OR);
    }

    /**
     * where field not in (list).
     *
     * @return static
     */
    public function whereNotIn(string $fieldName, array $list, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->where($fieldName, 'not in', $list, $logicalOperator);
    }

    /**
     * or where field not in (list).
     *
     * @return static
     */
    public function orWhereNotIn(string $fieldName, array $list): self
    {
        return $this->where($fieldName, 'not in', $list, LogicalOperator::OR);
    }

    /**
     * where field is null.
     *
     * @return static
     */
    public function whereIsNull(string $fieldName, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->whereRaw((new Field(null, null, $fieldName)) . ' is null', $logicalOperator);
    }

    /**
     * or where field is null.
     *
     * @return static
     */
    public function orWhereIsNull(string $fieldName): self
    {
        return $this->whereIsNull($fieldName, LogicalOperator::OR);
    }

    /**
     * where field is not null.
     *
     * @return static
     */
    public function whereIsNotNull(string $fieldName, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->whereRaw((new Field(null, null, $fieldName)) . ' is not null', $logicalOperator);
    }

    /**
     * or where field is not null.
     *
     * @return static
     */
    public function orWhereIsNotNull(string $fieldName): self
    {
        return $this->whereIsNotNull($fieldName, LogicalOperator::OR);
    }

    /**
     * join.
     *
     * @param string     $table      表名
     * @param string     $left       在 join b on a.id=b.id 中的 a.id
     * @param string     $operation  在 join b on a.id=b.id 中的 =
     * @param string     $right      在 join b on a.id=b.id 中的 b.id
     * @param string     $tableAlias 表别名
     * @param IBaseWhere $where      where条件
     * @param string     $type       join类型，默认inner
     *
     * @return static
     */
    public function join(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null, string $type = 'inner'): self
    {
        $this->option->join[] = new Join($table, $left, $operation, $right, $tableAlias, $where, $type);

        return $this;
    }

    /**
     * join，使用SQL原生语句.
     *
     * @return static
     */
    public function joinRaw(string $raw): self
    {
        $join = new Join();
        $join->useRaw();
        $join->setRawSQL($raw);
        $this->option->join[] = $join;

        return $this;
    }

    /**
     * left join.
     *
     * @param string     $table      表名
     * @param string     $left       在 join b on a.id=b.id 中的 a.id
     * @param string     $operation  在 join b on a.id=b.id 中的 =
     * @param string     $right      在 join b on a.id=b.id 中的 b.id
     * @param string     $tableAlias 表别名
     * @param IBaseWhere $where      where条件
     *
     * @return static
     */
    public function leftJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self
    {
        return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'left');
    }

    /**
     * right join.
     *
     * @param string     $table      表名
     * @param string     $left       在 join b on a.id=b.id 中的 a.id
     * @param string     $operation  在 join b on a.id=b.id 中的 =
     * @param string     $right      在 join b on a.id=b.id 中的 b.id
     * @param string     $tableAlias 表别名
     * @param IBaseWhere $where      where条件
     *
     * @return static
     */
    public function rightJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self
    {
        return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'right');
    }

    /**
     * cross join.
     *
     * @param string     $table      表名
     * @param string     $left       在 join b on a.id=b.id 中的 a.id
     * @param string     $operation  在 join b on a.id=b.id 中的 =
     * @param string     $right      在 join b on a.id=b.id 中的 b.id
     * @param string     $tableAlias 表别名
     * @param IBaseWhere $where      where条件
     *
     * @return static
     */
    public function crossJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self
    {
        return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'cross');
    }

    /**
     * 排序.
     *
     * @return static
     */
    public function order(string $field, string $direction = 'asc'): self
    {
        $this->option->order[] = new Order($field, $direction);

        return $this;
    }

    /**
     * 排序
     * 支持的写法：
     * 1. id desc, age asc
     * 2. ['id'=>'desc', 'age'] // 缺省asc.
     *
     * @param string|array $raw
     *
     * @return static
     */
    public function orderRaw($raw): self
    {
        $optionOrder = &$this->option->order;
        if (\is_array($raw))
        {
            foreach ($raw as $k => $v)
            {
                if (is_numeric($k))
                {
                    $fieldName = $v;
                    $direction = 'asc';
                }
                else
                {
                    $fieldName = $k;
                    $direction = $v;
                }
                $optionOrder[] = new Order($fieldName, $direction);
            }
        }
        else
        {
            $order = new Order();
            $order->useRaw();
            $order->setRawSQL($raw);
            $optionOrder[] = $order;
        }

        return $this;
    }

    /**
     * 设置分页
     * 传入当前页码和每页显示数量，自动计算offset和limit.
     *
     * @return static
     */
    public function page(?int $page, ?int $count): self
    {
        $pagination = new Pagination($page, $count);
        $option = $this->option;
        $option->offset = $pagination->getLimitOffset();
        $option->limit = $count;

        return $this;
    }

    /**
     * 设置记录从第几个开始取出.
     *
     * @return static
     */
    public function offset(?int $offset): self
    {
        $this->option->offset = $offset;

        return $this;
    }

    /**
     * 设置查询几条记录.
     *
     * @return static
     */
    public function limit(?int $limit): self
    {
        $this->option->limit = $limit;

        return $this;
    }

    /**
     * group by.
     *
     * @param string ...$groups
     *
     * @return static
     */
    public function group(string ...$groups): self
    {
        $optionGroup = &$this->option->group;
        foreach ($groups as $item)
        {
            $group = new Group();
            $group->setValue($item);
            $optionGroup[] = $group;
        }

        return $this;
    }

    /**
     * group by，使用SQL原生语句.
     *
     * @return static
     */
    public function groupRaw(string $raw): self
    {
        $group = new Group();
        $group->useRaw();
        $group->setRawSQL($raw);
        $this->option->group[] = $group;

        return $this;
    }

    /**
     * 设置 having 条件.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function having(string $fieldName, string $operation, $value, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->having[] = new Having($fieldName, $operation, $value, $logicalOperator);

        return $this;
    }

    /**
     * 设置 having 条件，用原生语句.
     *
     * @return static
     */
    public function havingRaw(string $raw, string $logicalOperator = LogicalOperator::AND): self
    {
        $having = new Having();
        $having->useRaw();
        $having->setRawSQL($raw);
        $having->setLogicalOperator($logicalOperator);
        $this->option->having[] = $having;

        return $this;
    }

    /**
     * 设置 having 条件，传入回调，回调中的条件加括号.
     *
     * @return static
     */
    public function havingBrackets(callable $callback, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->having[] = new HavingBrackets($callback, $logicalOperator);

        return $this;
    }

    /**
     * 设置 having 条件，使用 IHaving 结构.
     *
     * @return static
     */
    public function havingStruct(IHaving $having, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->having[] = $having;

        return $this;
    }

    /**
     * 绑定预处理参数.
     *
     * @param string|int $name
     * @param mixed      $value
     *
     * @return static
     */
    public function bindValue($name, $value, int $dataType = \PDO::PARAM_STR): self
    {
        $this->binds[$name] = $value;

        return $this;
    }

    /**
     * 批量绑定预处理参数.
     *
     * @return static
     */
    public function bindValues(array $values): self
    {
        $binds = &$this->binds;
        foreach ($values as $k => $v)
        {
            $binds[$k] = $v;
        }

        return $this;
    }

    /**
     * 获取绑定预处理参数关系.
     */
    public function getBinds(): array
    {
        return $this->binds;
    }

    /**
     * 查询记录.
     */
    public function select(): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new SelectBuilder($this);
            $sql = $builder->build();
            if ($alias)
            {
                // @phpstan-ignore-next-line
                $originBinds = $binds;
                $binds = $this->binds;
                if ($binds)
                {
                    $this->binds = array_merge($originBinds, $binds);
                }
                else
                {
                    $this->binds = $originBinds;
                }
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        }
        if (!$this->isInitQueryType && !$this->isInTransaction())
        {
            $this->queryType = QueryType::READ;
        }

        return $this->execute($sql);
    }

    /**
     * 分页查询.
     */
    public function paginate(int $page, int $count, array $options = []): IPaginateResult
    {
        if ($options['total'] ?? true)
        {
            $total = (int) (clone $this)->count();
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
     * 插入记录.
     *
     * @param array|object|null $data
     */
    public function insert($data = null): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
            $bindValues = [];
            $numberKey = isset($data[0]);
            foreach ($data as $k => $v)
            {
                if ($numberKey)
                {
                    $bindValues[':' . ($k + 1)] = $v;
                }
                else
                {
                    $bindValues[':' . $k] = $v;
                }
            }
            $this->bindValues($bindValues);
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new InsertBuilder($this);
            $sql = $builder->build($data);
            if ($alias)
            {
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    // @phpstan-ignore-next-line
                    'binds' => $binds,
                ];
            }
        }

        return $this->execute($sql);
    }

    /**
     * 批量插入数据
     * 以第 0 个成员作为字段标准.
     *
     * @param array|object|null $data
     */
    public function batchInsert($data = null): IResult
    {
        $builder = new BatchInsertBuilder($this);
        $sql = $builder->build($data);

        return $this->execute($sql);
    }

    /**
     * 更新记录.
     *
     * @param array|object|null $data
     */
    public function update($data = null): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
            $bindValues = [];
            foreach ($data as $k => $v)
            {
                $bindValues[':' . $k] = $v;
            }
            $this->bindValues($bindValues);
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new UpdateBuilder($this);
            $sql = $builder->build($data);
            if ($alias)
            {
                // @phpstan-ignore-next-line
                $originBinds = $binds;
                $binds = $this->binds;
                if ($binds)
                {
                    $this->binds = array_merge($originBinds, $binds);
                }
                else
                {
                    $this->binds = $originBinds;
                }
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        }

        return $this->execute($sql);
    }

    /**
     * 替换数据（Replace）.
     *
     * @param array|object|null $data
     */
    public function replace($data = null): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
            $bindValues = [];
            foreach ($data as $k => $v)
            {
                $bindValues[':' . $k] = $v;
            }
            $this->bindValues($bindValues);
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new ReplaceBuilder($this);
            $sql = $builder->build($data);
            if ($alias)
            {
                // @phpstan-ignore-next-line
                $originBinds = $binds;
                $binds = $this->binds;
                if ($binds)
                {
                    $this->binds = array_merge($originBinds, $binds);
                }
                else
                {
                    $this->binds = $originBinds;
                }
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        }

        return $this->execute($sql);
    }

    /**
     * 删除记录.
     */
    public function delete(): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new DeleteBuilder($this);
            $sql = $builder->build();
            if ($alias)
            {
                // @phpstan-ignore-next-line
                $originBinds = $binds;
                $binds = $this->binds;
                if ($binds)
                {
                    $this->binds = array_merge($originBinds, $binds);
                }
                else
                {
                    $this->binds = $originBinds;
                }
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        }
        $result = $this->execute($sql);

        return $result;
    }

    /**
     * 统计数量.
     */
    public function count(string $field = '*'): int
    {
        return (int) $this->aggregate('count', $field);
    }

    /**
     * 求和.
     *
     * @return int|float
     */
    public function sum(string $field)
    {
        return $this->aggregate('sum', $field);
    }

    /**
     * 平均值
     *
     * @return int|float
     */
    public function avg(string $field)
    {
        return $this->aggregate('avg', $field);
    }

    /**
     * 最大值
     *
     * @return int|float
     */
    public function max(string $field)
    {
        return $this->aggregate('max', $field);
    }

    /**
     * 最小值
     *
     * @return int|float
     */
    public function min(string $field)
    {
        return $this->aggregate('min', $field);
    }

    /**
     * 聚合函数.
     *
     * @return mixed
     */
    public function aggregate(string $functionName, string $fieldName)
    {
        $field = new Field();
        $field->useRaw();
        $field->setRawSQL($functionName . '(' . $field->parseKeyword($fieldName) . ')');
        $this->option->field = [
            $field,
        ];

        return $this->select()->getScalar();
    }

    /**
     * 执行SQL语句.
     */
    public function execute(string $sql): IResult
    {
        try
        {
            if (null === $this->queryType)
            {
                $this->queryType = QueryType::WRITE;
            }
            $db = &$this->db;
            if (!$this->isInitDb)
            {
                $db = Db::getInstance($this->poolName, $this->queryType);
            }
            if (!$db)
            {
                return new $this->resultClass(false);
            }
            $stmt = $db->prepare($sql);
            $binds = $this->binds;
            $this->binds = [];
            $stmt->execute($binds);

            return new $this->resultClass($stmt, $this->modelClass);
        }
        finally
        {
            $this->__init();
        }
    }

    /**
     * 获取自动起名的参数名称.
     */
    public function getAutoParamName(): string
    {
        $dbParamInc = &$this->dbParamInc;
        if ($dbParamInc >= 65535)
        { // 限制dechex()结果最长为ffff，一般一个查询也不会用到这么多参数，足够了
            $dbParamInc = 0;
        }
        ++$dbParamInc;

        return ':p' . dechex($dbParamInc);
    }

    /**
     * 设置update/insert/replace数据.
     *
     * @param array|\Imi\Db\Query\Interfaces\IQuery $data
     *
     * @return static
     */
    public function setData($data): self
    {
        $this->option->saveData = $data;

        return $this;
    }

    /**
     * 设置update/insert/replace的字段.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function setField(string $fieldName, $value): self
    {
        $this->option->saveData[$fieldName] = $value;

        return $this;
    }

    /**
     * 设置update/insert/replace的字段，值为表达式，原样代入.
     *
     * @return static
     */
    public function setFieldExp(string $fieldName, string $exp): self
    {
        $this->option->saveData[$fieldName] = new Raw($exp);

        return $this;
    }

    /**
     * 设置递增字段.
     *
     * @return static
     */
    public function setFieldInc(string $fieldName, float $incValue = 1): self
    {
        $this->option->saveData[$fieldName] = new Raw(new Field($fieldName) . ' + ' . $incValue);

        return $this;
    }

    /**
     * 设置递减字段.
     *
     * @return static
     */
    public function setFieldDec(string $fieldName, float $decValue = 1): self
    {
        $this->option->saveData[$fieldName] = new Raw(new Field($fieldName) . ' - ' . $decValue);

        return $this;
    }

    /**
     * 当前主库连接是否在事务中.
     */
    private function isInTransaction(): bool
    {
        $poolName = $this->poolName;
        if (null === $poolName)
        {
            $poolName = Db::getDefaultPoolName();
        }

        return Db::getInstance($poolName)->inTransaction();
    }

    /**
     * 查询器别名.
     *
     * @param callable|null $callable
     *
     * @return static
     */
    public function alias(string $name, $callable = null): self
    {
        $aliasMap = &static::$aliasMap;
        if (!isset($aliasMap[$name]))
        {
            if ($callable)
            {
                $callable($this);
            }
            $this->alias = $name;
            $aliasMap[$name] = $this;
        }

        return clone $aliasMap[$name];
    }

    /**
     * 加锁
     *
     * @param int|string|bool|null $value
     *
     * @return static
     */
    public function lock($value): self
    {
        $this->option->lock = $value;

        return $this;
    }

    /**
     * 设置结果集类名.
     *
     * @return static
     */
    public function setResultClass(string $resultClass)
    {
        $this->resultClass = $resultClass;

        return $this;
    }

    /**
     * 获取结果集类名.
     */
    public function getResultClass(): string
    {
        return $this->resultClass;
    }
}
