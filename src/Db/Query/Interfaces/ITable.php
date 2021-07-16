<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface ITable extends IDatabase
{
    /**
     * 获取表名.
     */
    public function getTable(): ?string;

    /**
     * 设置表名.
     */
    public function setTable(?string $table = null): void;

    /**
     * 设置值，可以根据传入的值自动处理
     * name——table
     * parent.name——database.table
     * name alias——table alias
     * name as alias—— table as alias.
     */
    public function setValue(string $value, IQuery $query): void;
}
