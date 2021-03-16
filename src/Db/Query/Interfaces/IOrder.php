<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IOrder extends IBase
{
    /**
     * 获取字段名.
     */
    public function getFieldName(): string;

    /**
     * 获取排序方向：asc/desc.
     */
    public function getDirection(): string;

    /**
     * 设置字段名.
     */
    public function setFieldName(string $fieldName): void;

    /**
     * 设置排序方向：asc/desc.
     */
    public function setDirection(string $direction): void;
}
