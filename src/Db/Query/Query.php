<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\App;
use Imi\Db\Db;
use Imi\Db\Interfaces\IDb;
use Imi\Db\Mysql\Consts\LogicalOperator;
use Imi\Db\Query\Having\Having;
use Imi\Db\Query\Having\HavingBrackets;
use Imi\Db\Query\Interfaces\IBaseWhere;
use Imi\Db\Query\Interfaces\IHaving;
use Imi\Db\Query\Interfaces\IPaginateResult;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\Where\Where;
use Imi\Db\Query\Where\WhereBrackets;
use Imi\Util\Pagination;

abstract class Query implements IQuery
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
     */
    protected static array $aliasSqlMap = [];

    public function __construct(?IDb $db = null, ?string $modelClass = null, ?string $poolName = null, ?int $queryType = null)
    {
        $this->db = $db;
        $this->isInitDb = null !== $db;
        $this->poolName = $poolName;
        $this->modelClass = $modelClass;
        $this->queryType = $queryType ?? QueryType::WRITE;
        $this->isInitQueryType = null !== $queryType;
    }

    public function __init(): void
    {
        $this->dbParamInc = 0;
        $this->option = new QueryOption();
        if (!$this->isInitQueryType)
        {
            $this->queryType = QueryType::WRITE;
        }
    }

    public function __clone()
    {
        $this->isInitDb = false;
        $this->db = null;
        $this->option = clone $this->option;
    }

    public static function newInstance(?IDb $db = null, ?string $modelClass = null, ?string $poolName = null, ?int $queryType = null): self
    {
        return App::getBean(static::class, $db, $modelClass, $poolName, $queryType);
    }

    /**
     * {@inheritDoc}
     */
    public function getOption(): QueryOption
    {
        return $this->option;
    }

    /**
     * {@inheritDoc}
     */
    public function setOption($option): self
    {
        $this->dbParamInc = 0;
        $this->option = $option;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDb(): IDb
    {
        return $this->db;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function from(string $table, string $alias = null, string $database = null): self
    {
        return $this->table($table, $alias, $database);
    }

    /**
     * {@inheritDoc}
     */
    public function fromRaw(string $raw): self
    {
        return $this->tableRaw($raw);
    }

    /**
     * {@inheritDoc}
     */
    public function distinct(bool $isDistinct = true): self
    {
        $this->option->distinct = $isDistinct;

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function where(string $fieldName, string $operation, $value, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->where[] = new Where($fieldName, $operation, $value, $logicalOperator);

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function whereBrackets(callable $callback, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->where[] = new WhereBrackets($callback, $logicalOperator);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function whereStruct(IBaseWhere $where, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->where[] = $where;

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function whereBetween(string $fieldName, $begin, $end, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->where($fieldName, 'between', [$begin, $end], $logicalOperator);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereBetween(string $fieldName, $begin, $end): self
    {
        return $this->where($fieldName, 'between', [$begin, $end], LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function whereNotBetween(string $fieldName, $begin, $end, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->where($fieldName, 'not between', [$begin, $end], $logicalOperator);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereNotBetween(string $fieldName, $begin, $end): self
    {
        return $this->where($fieldName, 'not between', [$begin, $end], LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhere(string $fieldName, string $operation, $value): self
    {
        return $this->where($fieldName, $operation, $value, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereRaw(string $where): self
    {
        return $this->whereRaw($where, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereBrackets(callable $callback): self
    {
        return $this->whereBrackets($callback, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereStruct(IBaseWhere $where): self
    {
        return $this->whereStruct($where, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereEx(array $condition): self
    {
        return $this->whereEx($condition, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function whereIn(string $fieldName, array $list, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->where($fieldName, 'in', $list, $logicalOperator);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereIn(string $fieldName, array $list): self
    {
        return $this->where($fieldName, 'in', $list, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function whereNotIn(string $fieldName, array $list, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->where($fieldName, 'not in', $list, $logicalOperator);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereNotIn(string $fieldName, array $list): self
    {
        return $this->where($fieldName, 'not in', $list, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function whereIsNull(string $fieldName, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->whereRaw($this->fieldQuote($fieldName) . ' is null', $logicalOperator);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereIsNull(string $fieldName): self
    {
        return $this->whereIsNull($fieldName, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function whereIsNotNull(string $fieldName, string $logicalOperator = LogicalOperator::AND): self
    {
        return $this->whereRaw($this->fieldQuote($fieldName) . ' is not null', $logicalOperator);
    }

    /**
     * {@inheritDoc}
     */
    public function orWhereIsNotNull(string $fieldName): self
    {
        return $this->whereIsNotNull($fieldName, LogicalOperator::OR);
    }

    /**
     * {@inheritDoc}
     */
    public function join(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null, string $type = 'inner'): self
    {
        $this->option->join[] = new Join($this, $table, $left, $operation, $right, $tableAlias, $where, $type);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function joinRaw(string $raw): self
    {
        $join = new Join($this);
        $join->useRaw();
        $join->setRawSQL($raw);
        $this->option->join[] = $join;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function leftJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self
    {
        return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'left');
    }

    /**
     * {@inheritDoc}
     */
    public function rightJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self
    {
        return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'right');
    }

    /**
     * {@inheritDoc}
     */
    public function crossJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self
    {
        return $this->join($table, $left, $operation, $right, $tableAlias, $where, 'cross');
    }

    /**
     * {@inheritDoc}
     */
    public function order(string $field, string $direction = 'asc'): self
    {
        $this->option->order[] = new Order($field, $direction);

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function offset(?int $offset): self
    {
        $this->option->offset = $offset;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function limit(?int $limit): self
    {
        $this->option->limit = $limit;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function group(string ...$groups): self
    {
        $optionGroup = &$this->option->group;
        foreach ($groups as $item)
        {
            $group = new Group();
            $group->setValue($item, $this);
            $optionGroup[] = $group;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function having(string $fieldName, string $operation, $value, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->having[] = new Having($fieldName, $operation, $value, $logicalOperator);

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function havingBrackets(callable $callback, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->having[] = new HavingBrackets($callback, $logicalOperator);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function havingStruct(IHaving $having, string $logicalOperator = LogicalOperator::AND): self
    {
        $this->option->having[] = $having;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue($name, $value, int $dataType = \PDO::PARAM_STR): self
    {
        $this->binds[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getBinds(): array
    {
        return $this->binds;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function count(string $field = '*'): int
    {
        return (int) $this->aggregate('count', $field);
    }

    /**
     * {@inheritDoc}
     */
    public function sum(string $field)
    {
        return $this->aggregate('sum', $field);
    }

    /**
     * {@inheritDoc}
     */
    public function avg(string $field)
    {
        return $this->aggregate('avg', $field);
    }

    /**
     * {@inheritDoc}
     */
    public function max(string $field)
    {
        return $this->aggregate('max', $field);
    }

    /**
     * {@inheritDoc}
     */
    public function min(string $field)
    {
        return $this->aggregate('min', $field);
    }

    /**
     * {@inheritDoc}
     */
    public function aggregate(string $functionName, string $fieldName)
    {
        $field = new Field();
        $field->useRaw();
        $field->setRawSQL($functionName . '(' . $this->fieldQuote($fieldName) . ')');
        $this->option->field = [
            $field,
        ];

        return $this->select()->getScalar();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(string $sql): IResult
    {
        try
        {
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function setData($data): self
    {
        $this->option->saveData = $data;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setField(string $fieldName, $value): self
    {
        $this->option->saveData[$fieldName] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setFieldExp(string $fieldName, string $exp): self
    {
        $this->option->saveData[$fieldName] = new Raw($exp);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setFieldInc(string $fieldName, float $incValue = 1): self
    {
        $this->option->saveData[$fieldName] = new Raw($this->fieldQuote($fieldName) . ' + ' . $incValue);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setFieldDec(string $fieldName, float $decValue = 1): self
    {
        $this->option->saveData[$fieldName] = new Raw($this->fieldQuote($fieldName) . ' - ' . $decValue);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function isInTransaction(): bool
    {
        return QueryType::WRITE === $this->queryType && Db::getInstance($this->poolName)->inTransaction();
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function lock($value): self
    {
        $this->option->lock = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setResultClass(string $resultClass)
    {
        $this->resultClass = $resultClass;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getResultClass(): string
    {
        return $this->resultClass;
    }
}
