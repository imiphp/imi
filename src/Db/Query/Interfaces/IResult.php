<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

use Imi\Db\Interfaces\IStatement;

interface IResult
{
    /**
     * SQL是否执行成功
     */
    public function isSuccess(): bool;

    /**
     * 获取最后插入的ID.
     *
     * @return int|string
     */
    public function getLastInsertId();

    /**
     * 获取影响行数.
     */
    public function getAffectedRows(): int;

    /**
     * 返回一行数据，数组或对象
     *
     * @param string|null $className 实体类名，为null则返回数组
     *
     * @return mixed
     */
    public function get(?string $className = null);

    /**
     * 返回数组.
     *
     * @param string|null $className 实体类名，为null则数组每个成员为数组
     */
    public function getArray(?string $className = null): array;

    /**
     * 获取一列.
     *
     * @param string|int $column
     */
    public function getColumn($column = 0): array;

    /**
     * 获取标量结果.
     *
     * @param int|string $columnKey
     *
     * @return mixed
     */
    public function getScalar($columnKey = 0);

    /**
     * 获取记录行数.
     */
    public function getRowCount(): int;

    /**
     * 获取执行的SQL语句.
     */
    public function getSql(): string;

    /**
     * 获取结果集对象
     */
    public function getStatement(): IStatement;

    /**
     * 获取原始结果集.
     */
    public function getStatementRecords(): array;
}
