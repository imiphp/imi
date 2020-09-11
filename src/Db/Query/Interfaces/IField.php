<?php

namespace Imi\Db\Query\Interfaces;

interface IField extends ITable
{
    /**
     * 获取字段名.
     *
     * @return string
     */
    public function getField(): string;

    /**
     * 设置字段名.
     *
     * @param string $field
     *
     * @return void
     */
    public function setField(string $field = null);

    /**
     * 设置值，可以根据传入的值自动处理
     * name——field
     * parent.name——table.field
     * parent.parent.name——database.table.field
     * name alias——field alias
     * name as alias—— field as alias.
     *
     * @param string $value
     *
     * @return void
     */
    public function setValue($value);
}
