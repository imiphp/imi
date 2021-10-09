<?php

declare(strict_types=1);

namespace Imi\Db\Query\Where;

use Imi\Db\Query\Interfaces\IQuery;
use Imi\Log\Log;

abstract class BaseWhere
{
    /**
     * 逻辑运算符.
     */
    protected string $logicalOperator = '';

    public function toString(IQuery $query): string
    {
        Log::warning(sprintf('%s object can not be used as string', static::class));

        return '';
    }
}
