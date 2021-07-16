<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IField extends ITable
{
    /**
     * 获取字段名.
     */
    public function getField(): ?string;

    /**
     * 设置字段名.
     *
     * @param string $field
     */
    public function setField(string $field = null): void;

    /**
     * 设置值，可以根据传入的值自动处理
     * name——field
     * parent.name——table.field
     * parent.parent.name——database.table.field
     * name alias——field alias
     * name as alias—— field as alias.
     */
    public function setValue(string $value, IQuery $query): void;
}
