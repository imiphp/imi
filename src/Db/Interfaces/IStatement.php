<?php

namespace Imi\Db\Interfaces;

interface IStatement extends \Iterator
{
    /**
     * 关闭.
     *
     * @return void
     */
    public function close();

    /**
     * 绑定一列到一个 PHP 变量.
     *
     * @param mixed $column
     * @param mixed $param
     * @param int   $type
     * @param int   $maxLen
     * @param mixed $driverData
     *
     * @return bool
     */
    public function bindColumn($column, &$param, int $type = null, int $maxLen = null, $driverData = null): bool;

    /**
     * 绑定一个参数到指定的变量名.
     *
     * @param mixed $parameter
     * @param mixed $variable
     * @param int   $dataType
     * @param int   $length
     * @param mixed $driverOptions
     *
     * @return bool
     */
    public function bindParam($parameter, &$variable, int $dataType = \PDO::PARAM_STR, int $length = null, $driverOptions = null): bool;

    /**
     * 把一个值绑定到一个参数.
     *
     * @param mixed $parameter
     * @param mixed $value
     * @param int   $dataType
     *
     * @return bool
     */
    public function bindValue($parameter, $value, int $dataType = \PDO::PARAM_STR): bool;

    /**
     * 关闭游标，使语句能再次被执行。
     *
     * @return bool
     */
    public function closeCursor(): bool;

    /**
     * 返回结果集中的列数.
     *
     * @return int
     */
    public function columnCount(): int;

    /**
     * 返回错误码
     *
     * @return mixed
     */
    public function errorCode();

    /**
     * 返回错误信息.
     *
     * @return array
     */
    public function errorInfo(): string;

    /**
     * 获取SQL语句.
     *
     * @return string
     */
    public function getSql();

    /**
     * 执行一条预处理语句.
     *
     * @param array $inputParameters
     *
     * @return bool
     */
    public function execute(array $inputParameters = null): bool;

    /**
     * 从结果集中获取下一行.
     *
     * @param int $fetchStyle
     * @param int $cursorOrientation
     * @param int $cursorOffset
     *
     * @return mixed
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0);

    /**
     * 返回一个包含结果集中所有行的数组.
     *
     * @param int   $fetchStyle
     * @param mixed $fetchArgument
     * @param array $ctorArgs
     *
     * @return array
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = null, array $ctorArgs = []): array;

    /**
     * 从结果集中的下一行返回单独的一列。
     *
     * @param int|string $columnKey
     *
     * @return mixed
     */
    public function fetchColumn($columnKey = 0);

    /**
     * 获取下一行并作为一个对象返回。
     *
     * @param string $class_name
     * @param array  $ctor_args
     *
     * @return mixed
     */
    public function fetchObject(string $className = 'stdClass', array $ctorArgs = null);

    /**
     * 检索一个语句属性.
     *
     * @param mixed $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute);

    /**
     * 设置属性.
     *
     * @param mixed $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function setAttribute($attribute, $value): bool;

    /**
     * 在一个多行集语句句柄中推进到下一个行集.
     *
     * @return bool
     */
    public function nextRowset(): bool;

    /**
     * 返回最后插入行的ID或序列值
     *
     * @param string $name
     *
     * @return string
     */
    public function lastInsertId(string $name = null);

    /**
     * 返回受上一个 SQL 语句影响的行数.
     *
     * @return int
     */
    public function rowCount(): int;

    /**
     * 获取原对象实例.
     *
     * @return object
     */
    public function getInstance();

    /**
     * 获取数据库操作对象
     *
     * @return IDb
     */
    public function getDb(): IDb;
}
