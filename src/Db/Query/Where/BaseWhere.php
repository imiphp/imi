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

    public function toString(IQuery $query): string
    {
        trigger_error(sprintf('%s object can not be used as string', static::class), \E_USER_ERROR);

        return '';
    }
}
