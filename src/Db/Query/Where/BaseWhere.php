<?php

declare(strict_types=1);

namespace Imi\Db\Query\Where;

use Imi\Db\Query\Interfaces\IQuery;

abstract class BaseWhere
{
    /**
     * 逻辑运算符.
     */
    protected string $logicalOperator = '';

    public function getLogicalOperator(): string
    {
        return $this->logicalOperator;
    }

    public function setLogicalOperator(string $logicalOperator): void
    {
        $this->logicalOperator = $logicalOperator;
    }

    public function toString(IQuery $query): string
    {
        throw new \RuntimeException(sprintf('%s object can not be used as string', static::class)); // @codeCoverageIgnore
    }
}
