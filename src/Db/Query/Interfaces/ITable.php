<?php

namespace Imi\Db\Query\Interfaces;

interface ITable extends IDatabase
{
    /**
     * 获取表名.
     *
     * @return string
     */
    public function getTable(): string;

    /**
     * 设置表名.
     *
     * @param string $table
     *
     * @return void
     */
    public function setTable(string $table = null);

    /**
     * 设置值，可以根据传入的值自动处理
     * name——table
     * parent.name——database.table
     * name alias——table alias
     * name as alias—— table as alias.
     *
     * @param string $value
     *
     * @return void
     */
    public function setValue($value);
}
