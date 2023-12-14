<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IWhereCollector extends IBaseWhereCollector
{
    /**
     * @return IBaseWhere[]
     */
    public function getWhere(): array;
}
