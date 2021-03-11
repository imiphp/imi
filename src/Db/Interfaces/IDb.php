<?php

namespace Imi\Db\Interfaces;

use Imi\Util\Interfaces\IHashCode;

interface IDb extends IHashCode
{
    /**
     * 打开
     *
     * @return bool
     */
    public function open();

    /**
     * 关闭.
     *
     * @return void
     */
    public function close();

    /**
     * 是否已连接.
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * 启动一个事务
     *
     * @return bool
     */
    public function beginTransaction(): bool;

    /**
     * 提交一个事务
     *
     * @return bool
     */
    public function commit(): bool;

    /**
     * 回滚事务
     * 支持设置回滚事务层数，如果不设置则为全部回滚.
     *
     * @param int $levels
     *
     * @return bool
     */
    public function rollBack($levels = null): bool;

    /**
     * 获取事务层数.
     *
     * @return int
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
     *
     * @return string
     */
    public function errorInfo(): string;

    /**
     * 获取最后一条执行的SQL语句.
     *
     * @return string
     */
    public function lastSql();

    /**
     * 执行一条 SQL 语句，并返回受影响的行数.
     *
     * @param string $sql
     *
     * @return int
     */
    public function exec(string $sql): int;

    /**
     * 批量执行 SQL，返回查询结果.
     *
     * @param string $sql
     *
     * @return array
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
     *
     * @return bool
     */
    public function setAttribute($attribute, $value);

    /**
     * 检查是否在一个事务内.
     *
     * @return bool
     */
    public function inTransaction(): bool;

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
     * 准备执行语句并返回一个语句对象
     *
     * @param string $sql
     * @param array  $driverOptions
     *
     * @return IStatement|bool
     */
    public function prepare(string $sql, array $driverOptions = []);

    /**
     * 执行一条SQL语句，返回一个结果集作为PDOStatement对象
     *
     * @param string $sql
     *
     * @return IStatement|bool
     */
    public function query(string $sql);

    /**
     * 获取原对象实例.
     *
     * @return object
     */
    public function getInstance();

    /**
     * Get 事务管理.
     *
     * @return \Imi\Db\Transaction\Transaction
     */
    public function getTransaction();
}
