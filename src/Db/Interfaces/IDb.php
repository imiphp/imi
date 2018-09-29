<?php

namespace Imi\Db\Interfaces;

use Imi\Util\Defer;

interface IDb
{
    /**
     * 打开
     * @return boolean
     */
    public function open();

    /**
     * 关闭
     * @return void
     */
    public function close();

    /**
     * 是否已连接
     * @return boolean
     */
    public function isConnected(): bool;

    /**
     * 启动一个事务
     * @return boolean
     */
    public function beginTransaction(): bool;

    /**
     * 提交一个事务
     * @return boolean
     */
    public function commit(): bool;

    /**
     * 回滚一个事务
     * @return boolean
     */
    public function rollBack(): bool;

    /**
     * 返回错误码
     * @return mixed
     */
    public function errorCode();

    /**
     * 返回错误信息
     * @return array
     */
    public function errorInfo(): string;

    /**
     * 获取最后一条执行的SQL语句
     * @return string
     */
    public function lastSql();

    /**
     * 执行一条 SQL 语句，并返回受影响的行数
     *
     * @param string $sql
     *
     * @return integer
     */
    public function exec(string $sql): int;

    /**
     * 取回一个数据库连接的属性
     *
     * @param mixed $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute);

    /**
     * 设置属性
     *
     * @param mixed $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function setAttribute($attribute, $value);

    /**
     * 检查是否在一个事务内
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
     * 返回受上一个 SQL 语句影响的行数
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
     * 获取原对象实例
     * @return object
     */
    public function getInstance();

    /**
     * 启动一个事务
     * @return Defer
     */
    public function deferBeginTransaction(): Defer;

    /**
     * 提交一个事务
     * @return Defer
     */
    public function deferCommit(): Defer;

    /**
     * 回滚一个事务
     * @return Defer
     */
    public function deferRollBack(): Defer;

    /**
     * 执行一条 SQL 语句，并返回受影响的行数
     *
     * @param string $sql
     *
     * @return Defer
     */
    public function deferExec(string $sql): Defer;

    /**
     * 准备执行语句并返回一个语句对象
     *
     * @param string $sql
     * @param array $driverOptions
     *
     * @return Defer
     */
    public function deferPrepare(string $sql, array $driverOptions = []): Defer;

    /**
     * 执行一条SQL语句，返回一个结果集作为PDOStatement对象
     *
     * @param string $sql
     *
     * @return Defer
     */
    public function deferQuery(string $sql): Defer;
}