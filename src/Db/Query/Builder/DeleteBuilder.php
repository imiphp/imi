<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

class DeleteBuilder extends BaseBuilder
{
    public function build(...$args):string
    {
        parent::build(...$args);
        $query = $this->query;
        $option = $query->getOption();

        $sql = 'delete from ' . $option->table
                . $this->parseWhere($option->where)
                . $this->parseOrder($option->order)
                . $this->parseLimit($option->offset, $option->limit)
                ;
        $query->bindValues($this->params);

        return $sql;
    }
}
