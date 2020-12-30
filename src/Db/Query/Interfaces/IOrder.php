<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IOrder extends IBase
{
    /**
     * 获取字段名.
     *
     * @return string
     */
    public function getFieldName(): string;

    /**
     * 获取排序方向：asc/desc.
     *
     * @return string
     */
    public function getDirection(): string;

    /**
     * 设置字段名.
     *
     * @param string $fieldName
     *
     * @return void
     */
    public function setFieldName(string $fieldName);

    /**
     * 设置排序方向：asc/desc.
     *
     * @param string $direction
     *
     * @return void
     */
    public function setDirection(string $direction);
}
