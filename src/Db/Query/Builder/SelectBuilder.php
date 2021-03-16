<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

use Imi\Db\Query\Lock\MysqlLock;

class SelectBuilder extends BaseBuilder
{
    public function build(...$args): string
    {
        parent::build(...$args);
        $query = $this->query;
        $option = $query->getOption();
        $sql = 'select ' . $this->parseDistinct($option->distinct)
                . $this->parseField($option->field)
                . ' from '
                . $option->table
                . $this->parseJoin($option->join)
                . $this->parseWhere($option->where)
                . $this->parseGroup($option->group)
                . $this->parseHaving($option->having)
                . $this->parseOrder($option->order)
                . $this->parseLimit($option->offset, $option->limit)
                . $this->parseLock($option->lock)
                ;
        $query->bindValues($this->params);

        return $sql;
    }

    /**
     * lock.
     *
     * @param int|string|bool|null $lock
     */
    public function parseLock($lock): string
    {
        if (null === $lock || false === $lock)
        {
            return '';
        }
        switch ($lock)
        {
            case MysqlLock::FOR_UPDATE:
                return ' FOR UPDATE';
            case MysqlLock::SHARED:
                return ' LOCK IN SHARE MODE';
            default:
                return ' ' . $lock;
        }
    }
}
