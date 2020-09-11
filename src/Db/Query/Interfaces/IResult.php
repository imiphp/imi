<?php

namespace Imi\Db\Query\Interfaces;

use Imi\Db\Interfaces\IStatement;

interface IResult
{
    /**
     * SQL是否执行成功
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * 获取最后插入的ID.
     *
     * @return string
     */
    public function getLastInsertId();

    /**
     * 获取影响行数.
     *
     * @return int
     */
    public function getAffectedRows();

    /**
     * 返回一行数据，数组或对象
     *
     * @param string $className 实体类名，为null则返回数组
     *
     * @return mixed
     */
    public function get($className = null);

    /**
     * 返回数组.
     *
     * @param string $className 实体类名，为null则数组每个成员为数组
     *
     * @return array
     */
    public function getArray($className = null);

    /**
     * 获取一列.
     *
     * @return array
     */
    public function getColumn($column = 0);

    /**
     * 获取标量结果.
     *
     * @return mixed
     */
    public function getScalar();

    /**
     * 获取记录行数.
     *
     * @return int
     */
    public function getRowCount();

    /**
     * 获取执行的SQL语句.
     *
     * @return string
     */
    public function getSql();

    /**
     * 获取结果集对象
     *
     * @return \Imi\Db\Interfaces\IStatement
     */
    public function getStatement(): IStatement;
}
