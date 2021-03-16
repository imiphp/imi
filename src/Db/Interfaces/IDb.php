<?php

declare(strict_types=1);

namespace Imi\Db\Interfaces;

use Imi\Db\Transaction\Transaction;
use Imi\Util\Interfaces\IHashCode;

interface IDb extends IHashCode
{
    /**
     * 打开
     */
    public function open(): bool;

    /**
     * 关闭.
     */
    public function close(): void;

    /**
     * 是否已连接.
     */
    public function isConnected(): bool;

    /**
     * 启动一个事务
     */
    public function beginTransaction(): bool;

    /**
     * 提交一个事务
     */
    public function commit(): bool;

    /**
     * 回滚事务
     * 支持设置回滚事务层数，如果不设置则为全部回滚.
     */
    public function rollBack(?int $levels = null): bool;

    /**
     * 获取事务层数.
     */
    public function getTransactionLevels(): int;

    /**
     * 返回错误码
     *
     * @return mixed
     */
    public function errorCode();

    /**
     * 返回错误信息.
     */
    public function errorInfo(): string;

    /**
     * 获取最后一条执行的SQL语句.
     */
    public function lastSql(): string;

    /**
     * 执行一条 SQL 语句，并返回受影响的行数.
     */
    public function exec(string $sql): int;

    /**
     * 批量执行 SQL，返回查询结果.
     */
    public function batchExec(string $sql): array;

    /**
     * 取回一个数据库连接的属性.
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
     */
    public function setAttribute($attribute, $value): bool;

    /**
     * 检查是否在一个事务内.
     */
    public function inTransaction(): bool;

    /**
     * 返回最后插入行的ID或序列值
     */
    public function lastInsertId(?string $name = null): string;

    /**
     * 返回受上一个 SQL 语句影响的行数.
     */
    public function rowCount(): int;

    /**
     * 准备执行语句并返回一个语句对象
     */
    public function prepare(string $sql, array $driverOptions = []): IStatement;

    /**
     * 执行一条SQL语句，返回一个结果集作为PDOStatement对象
     */
    public function query(string $sql): IStatement;

    /**
     * 获取原对象实例.
     *
     * @return object
     */
    public function getInstance();

    /**
     * Get 事务管理.
     */
    public function getTransaction(): Transaction;
}
