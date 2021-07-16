<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IOrder;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Traits\TRaw;

class Order implements IOrder
{
    use TRaw;

    /**
     * 字段名.
     */
    protected string $fieldName = '';

    /**
     * 排序方向：asc/desc.
     */
    protected string $direction = '';

    public function __construct(string $fieldName = '', string $direction = 'asc')
    {
        $this->fieldName = $fieldName;
        $this->direction = $direction;
    }

    /**
     * 获取字段名.
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * 获取排序方向：asc/desc.
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * 设置字段名.
     */
    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    /**
     * 设置排序方向：asc/desc.
     */
    public function setDirection(string $direction): void
    {
        $this->direction = $direction;
    }

    public function toString(IQuery $query): string
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }

        return $query->fieldQuote($this->fieldName) . ' ' . $this->direction;
    }

    /**
     * 获取绑定的数据们.
     */
    public function getBinds(): array
    {
        return [];
    }
}
