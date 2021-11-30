<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

use Imi\Db\Interfaces\IDb;
use Imi\Db\Query\QueryOption;

/**
 * 查询器接口.
 */
interface IQuery
{
    /**
     * 获取所有操作的记录.
     *
     * @return QueryOption
     */
    public function getOption();

    /**
     * 设置操作记录.
     *
     * @param QueryOption $option
     *
     * @return static
     */
    public function setOption($option): self;

    /**
     * 获取数据库操作对象
     */
    public function getDb(): IDb;

    /**
     * 设置表名.
     *
     * @param string      $table    表名
     * @param string|null $alias    别名
     * @param string|null $database 数据库名
     *
     * @return static
     */
    public function table(string $table, string $alias = null, string $database = null): self;

    /**
     * 设置表名，使用SQL原生语句.
     *
     * @return static
     */
    public function tableRaw(string $raw, ?string $alias = null): self;

    /**
     * 设置表名，table()的别名.
     *
     * @param string      $table    表名
     * @param string      $alias    别名
     * @param string|null $database 数据库名
     *
     * @return static
     */
    public function from(string $table, string $alias = null, string $database = null): self;

    /**
     * 设置表名，使用SQL原生语句.
     *
     * @return static
     */
    public function fromRaw(string $raw): self;

    /**
     * 设置 distinct.
     *
     * @param bool $isDistinct 是否设置distinct
     *
     * @return static
     */
    public function distinct(bool $isDistinct = true): self;

    /**
     * 指定查询字段.
     *
     * @param string $fields 查询字段
     *
     * @return static
     */
    public function field(string ...$fields): self;

    /**
     * 指定查询字段，使用SQL原生语句.
     *
     * @return static
     */
    public function fieldRaw(string $raw, ?string $alias = null): self;

    /**
     * 设置 where 条件，一般用于 =、>、<、like等.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function where(string $fieldName, string $operation, $value, string $logicalOperator = 'and'): self;

    /**
     * 设置 where 条件，用原生语句.
     *
     * @return static
     */
    public function whereRaw(string $raw, string $logicalOperator = 'and'): self;

    /**
     * 设置 where 条件，传入回调，回调中的条件加括号.
     *
     * @return static
     */
    public function whereBrackets(callable $callback, string $logicalOperator = 'and'): self;

    /**
     * 设置 where 条件，使用 IBaseWhere 结构.
     *
     * @return static
     */
    public function whereStruct(IBaseWhere $where, string $logicalOperator = 'and'): self;

    /**
     * 设置 where 条件，支持语法如下：.
     *
     * [
     *      'id'    => 1,
     *      'or'    => [
     *          'id' => 2,
     *      ],
     *      'title' => ['like', '%test%'],
     *      'age'   => ['>', 18],
     *      'age'   => ['between', 19, 29]
     * ]
     *
     * SQL: id = 1 or (id = 2) and title like '%test%' and age > 18 and age between 19 and 29
     *
     * @return static
     */
    public function whereEx(array $condition, string $logicalOperator = 'and'): self;

    /**
     * where between $begin end $end.
     *
     * @param mixed $begin
     * @param mixed $end
     *
     * @return static
     */
    public function whereBetween(string $fieldName, $begin, $end, string $logicalOperator = 'and'): self;

    /**
     * or where between $begin end $end.
     *
     * @param mixed $begin
     * @param mixed $end
     *
     * @return static
     */
    public function orWhereBetween(string $fieldName, $begin, $end): self;

    /**
     * where not between $begin end $end.
     *
     * @param mixed $begin
     * @param mixed $end
     *
     * @return static
     */
    public function whereNotBetween(string $fieldName, $begin, $end, string $logicalOperator = 'and'): self;

    /**
     * or where not between $begin end $end.
     *
     * @param mixed $begin
     * @param mixed $end
     *
     * @return static
     */
    public function orWhereNotBetween(string $fieldName, $begin, $end): self;

    /**
     * 设置 where or 条件.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function orWhere(string $fieldName, string $operation, $value): self;

    /**
     * 设置 where or 条件，用原生语句.
     *
     * @return static
     */
    public function orWhereRaw(string $where): self;

    /**
     * 设置 where or 条件，传入回调，回调中的条件加括号.
     *
     * @return static
     */
    public function orWhereBrackets(callable $callback): self;

    /**
     * 设置 where or 条件，使用 IBaseWhere 结构.
     *
     * @return static
     */
    public function orWhereStruct(IBaseWhere $where): self;

    /**
     * 设置 where or 条件，支持语法参考 whereEx 方法.
     *
     * @return static
     */
    public function orWhereEx(array $condition): self;

    /**
     * where field in (list).
     *
     * @return static
     */
    public function whereIn(string $fieldName, array $list, string $logicalOperator = 'and'): self;

    /**
     * or where field in (list).
     *
     * @return static
     */
    public function orWhereIn(string $fieldName, array $list): self;

    /**
     * where field not in (list).
     *
     * @return static
     */
    public function whereNotIn(string $fieldName, array $list, string $logicalOperator = 'and'): self;

    /**
     * or where field not in (list).
     *
     * @return static
     */
    public function orWhereNotIn(string $fieldName, array $list): self;

    /**
     * where field is null.
     *
     * @return static
     */
    public function whereIsNull(string $fieldName, string $logicalOperator = 'and'): self;

    /**
     * or where field is null.
     *
     * @return static
     */
    public function orWhereIsNull(string $fieldName): self;

    /**
     * where field is not null.
     *
     * @return static
     */
    public function whereIsNotNull(string $fieldName, string $logicalOperator = 'and'): self;

    /**
     * or where field is not null.
     *
     * @return static
     */
    public function orWhereIsNotNull(string $fieldName): self;

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
    public function join(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null, string $type = 'inner'): self;

    /**
     * join，使用SQL原生语句.
     *
     * @return static
     */
    public function joinRaw(string $raw): self;

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
    public function leftJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self;

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
    public function rightJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self;

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
    public function crossJoin(string $table, string $left, string $operation, string $right, string $tableAlias = null, IBaseWhere $where = null): self;

    /**
     * 排序.
     *
     * @return static
     */
    public function order(string $field, string $direction = 'asc'): self;

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
    public function orderRaw($raw): self;

    /**
     * 设置分页
     * 传入当前页码和每页显示数量，自动计算offset和limit.
     *
     * @return static
     */
    public function page(?int $page, ?int $show): self;

    /**
     * 设置记录从第几个开始取出.
     *
     * @return static
     */
    public function offset(?int $offset): self;

    /**
     * 设置查询几条记录.
     *
     * @return static
     */
    public function limit(?int $limit): self;

    /**
     * group by.
     *
     * @param string ...$groups
     *
     * @return static
     */
    public function group(string ...$groups): self;

    /**
     * group by，使用SQL原生语句.
     *
     * @return static
     */
    public function groupRaw(string $raw): self;

    /**
     * 设置 having 条件.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function having(string $fieldName, string $operation, $value, string $logicalOperator = 'and'): self;

    /**
     * 设置 having 条件，用原生语句.
     *
     * @return static
     */
    public function havingRaw(string $raw, string $logicalOperator = 'and'): self;

    /**
     * 设置 having 条件，传入回调，回调中的条件加括号.
     *
     * @return static
     */
    public function havingBrackets(callable $callback, string $logicalOperator = 'and'): self;

    /**
     * 设置 having 条件，使用 IHaving 结构.
     *
     * @return static
     */
    public function havingStruct(IHaving $having, string $logicalOperator = 'and'): self;

    /**
     * 绑定预处理参数.
     *
     * @param string|int $name
     * @param mixed      $value
     *
     * @return static
     */
    public function bindValue($name, $value, int $dataType = \PDO::PARAM_STR): self;

    /**
     * 批量绑定预处理参数.
     *
     * @return static
     */
    public function bindValues(array $values): self;

    /**
     * 获取绑定预处理参数关系.
     */
    public function getBinds(): array;

    /**
     * 查询所有记录，返回数组.
     */
    public function select(): IResult;

    /**
     * 分页查询.
     *
     * @return \Imi\Db\Query\Interfaces\IPaginateResult
     */
    public function paginate(int $page, int $count, array $options = []): IPaginateResult;

    /**
     * 插入记录.
     *
     * @param array|object|null $data
     */
    public function insert($data = null): IResult;

    /**
     * 批量插入数据
     * 以第 0 个成员作为字段标准.
     *
     * @param array|object|null $data
     */
    public function batchInsert($data = null): IResult;

    /**
     * 更新数据.
     *
     * @param array|object|null $data
     */
    public function update($data = null): IResult;

    /**
     * 替换数据（Replace）.
     *
     * @param array|object|null $data
     */
    public function replace($data = null): IResult;

    /**
     * 删除数据.
     */
    public function delete(): IResult;

    /**
     * 执行SQL语句.
     */
    public function execute(string $sql): IResult;

    /**
     * 统计数量.
     */
    public function count(string $field = '*'): int;

    /**
     * 求和.
     *
     * @return int|float
     */
    public function sum(string $field);

    /**
     * 平均值
     *
     * @return int|float
     */
    public function avg(string $field);

    /**
     * 最大值
     *
     * @return int|float
     */
    public function max(string $field);

    /**
     * 最小值
     *
     * @return int|float
     */
    public function min(string $field);

    /**
     * 聚合函数.
     *
     * @return mixed
     */
    public function aggregate(string $functionName, string $fieldName);

    /**
     * 设置update/insert/replace数据.
     *
     * @param array|\Imi\Db\Query\Interfaces\IQuery $data
     *
     * @return static
     */
    public function setData($data): self;

    /**
     * 设置update/insert/replace的字段.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function setField(string $fieldName, $value): self;

    /**
     * 设置update/insert/replace的字段，值为表达式，原样代入.
     *
     * @return static
     */
    public function setFieldExp(string $fieldName, string $exp): self;

    /**
     * 设置递增字段.
     *
     * @return static
     */
    public function setFieldInc(string $fieldName, float $incValue = 1): self;

    /**
     * 设置递减字段.
     *
     * @return static
     */
    public function setFieldDec(string $fieldName, float $decValue = 1): self;

    /**
     * 获取自动起名的参数名称.
     */
    public function getAutoParamName(): string;

    /**
     * 查询器别名.
     *
     * @param callable|null $callable
     *
     * @return static
     */
    public function alias(string $name, $callable = null): self;

    /**
     * 加锁
     *
     * @param int|string|bool|null $value
     *
     * @return static
     */
    public function lock($value): self;

    /**
     * 设置结果集类名.
     *
     * @return static
     */
    public function setResultClass(string $resultClass);

    /**
     * 获取结果集类名.
     */
    public function getResultClass(): string;

    /**
     * 字段安全引用.
     */
    public function fieldQuote(string $name): string;

    /**
     * 把输入的关键字文本转为数组.
     */
    public function parseKeywordText(string $string): array;

    /**
     * 从数组拼装为有分隔标识符的关键字.
     */
    public function parseKeywordToText(array $keywords, ?string $alias = null, ?array $jsonKeywords = null): string;
}
