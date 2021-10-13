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
     * {@inheritDoc}
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * {@inheritDoc}
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * {@inheritDoc}
     */
    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    /**
     * {@inheritDoc}
     */
    public function setDirection(string $direction): void
    {
        $this->direction = $direction;
    }

    /**
     * {@inheritDoc}
     */
    public function toString(IQuery $query): string
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }

        return $query->fieldQuote($this->fieldName) . ' ' . $this->direction;
    }

    /**
     * {@inheritDoc}
     */
    public function getBinds(): array
    {
        return [];
    }
}
