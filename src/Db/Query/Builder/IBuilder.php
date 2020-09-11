<?php

namespace Imi\Db\Query\Builder;

interface IBuilder
{
    /**
     * 生成SQL语句.
     *
     * @param mixed $args
     *
     * @return string
     */
    public function build(...$args);
}
