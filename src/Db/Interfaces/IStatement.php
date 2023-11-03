<?php

declare(strict_types=1);

namespace Imi\Db\Interfaces;

interface IStatement extends \Iterator
{
    /**
     * 关闭.
     */
    public function close(): void;

    /**
     * 绑定一列到一个 PHP 变量.
     */
    public function bindColumn(string|int $column, mixed &$var, int $type = \PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool;

    /**
     * 绑定一个参数到指定的变量名.
     */
    public function bindParam(string|int $param, mixed &$var, int $type = \PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool;

    /**
     * 把一个值绑定到一个参数.
     */
    public function bindValue(string|int $param, mixed $value, int $type = \PDO::PARAM_STR): bool;

    /**
     * 关闭游标，使语句能再次被执行。
     */
    public function closeCursor(): bool;

    /**
     * 返回结果集中的列数.
     */
    public function columnCount(): int;

    /**
     * 返回错误码
     */
    public function errorCode(): mixed;

    /**
     * 返回错误信息.
     */
    public function errorInfo(): string;

    /**
     * 获取SQL语句.
     */
    public function getSql(): string;

    /**
     * 执行一条预处理语句.
     */
    public function execute(array $inputParameters = null): bool;

    /**
     * 从结果集中获取下一行.
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed;

    /**
     * 返回一个包含结果集中所有行的数组.
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, mixed $fetchArgument = null, array $ctorArgs = []): array;

    /**
     * 从结果集中的下一行返回单独的一列。
     */
    public function fetchColumn(int $column = 0): mixed;

    /**
     * 获取下一行并作为一个对象返回。
     */
    public function fetchObject(string $className = \stdClass::class, ?array $ctorArgs = null): mixed;

    /**
     * 检索一个语句属性.
     */
    public function getAttribute(mixed $attribute): mixed;

    /**
     * 设置属性.
     */
    public function setAttribute(mixed $attribute, mixed $value): bool;

    /**
     * 在一个多行集语句句柄中推进到下一个行集.
     */
    public function nextRowset(): bool;

    /**
     * 返回最后插入行的ID或序列值
     */
    public function lastInsertId(?string $name = null): string;

    /**
     * 返回受上一个 SQL 语句影响的行数.
     */
    public function rowCount(): int;

    /**
     * 获取原对象实例.
     */
    public function getInstance(): object;

    /**
     * 获取数据库操作对象
     */
    public function getDb(): IDb;
}
