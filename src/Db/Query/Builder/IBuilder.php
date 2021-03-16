<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

interface IBuilder
{
    /**
     * 生成SQL语句.
     *
     * @param mixed $args
     */
    public function build(...$args): string;
}
