<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

use Imi\Db\Query\Interfaces\IQuery;

interface IBuilder
{
    /**
     * @param IQuery|null $query IQuery类
     */
    public function __construct(?IQuery $query);

    /**
     * 生成SQL语句.
     */
    public function build(mixed ...$args): string;
}
