<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

use Imi\Db\Query\Interfaces\IQuery;

abstract class BaseBuilder implements IBuilder
{
    /**
     * IQuery 类.
     */
    protected IQuery $query;

    /**
     * 绑定参数.
     */
    protected array $params = [];

    /**
     * 生成SQL语句.
     *
     * @param mixed $args
     *
     * @return string
     */
    public static function buildSql(IQuery $query, ...$args)
    {
        $builder = new static($query);

        return $builder->build(...$args);
    }

    public function __construct(IQuery $query)
    {
        $this->query = $query;
    }

    /**
     * 生成SQL语句.
     *
     * @param mixed $args
     */
    public function build(...$args): string
    {
        $this->params = [];

        return '';
    }

    /**
     * distinct.
     */
    protected function parseDistinct(bool $distinct): string
    {
        return $distinct ? 'distinct ' : '';
    }

    /**
     * fields.
     */
    abstract protected function parseField(array $fields): string;

    /**
     * join.
     *
     * @param \Imi\Db\Query\Interfaces\IJoin[] $join
     */
    protected function parseJoin(array $join): string
    {
        if (!$join)
        {
            return '';
        }
        $result = implode(' ', $join);
        $params = &$this->params;
        foreach ($join as $item)
        {
            $binds = $item->getBinds();
            if ($binds)
            {
                $params = array_merge($params, $binds);
            }
        }

        return $result;
    }

    /**
     * where.
     *
     * @param \Imi\Db\Query\Interfaces\IBaseWhere[] $where
     */
    protected function parseWhere(array $where): string
    {
        if (!$where)
        {
            return '';
        }
        $result = [];
        $params = &$this->params;
        $query = $this->query;
        foreach ($where as $item)
        {
            $result[] = $item->getLogicalOperator();
            $result[] = $item->toStringWithoutLogic($query);
            $binds = $item->getBinds();
            if ($binds)
            {
                $params = array_merge($params, $binds);
            }
        }
        unset($result[0]);
        $result = implode(' ', $result);
        if ('' !== $result)
        {
            $result = ' where ' . $result;
        }

        return $result;
    }

    /**
     * limit.
     *
     * @return string
     */
    protected function parseLimit(?int $offset, ?int $limit)
    {
        if (null === $limit)
        {
            return '';
        }
        elseif (null === $offset)
        {
            return ' limit ' . ((int) $limit);
        }
        else
        {
            return ' limit ' . ((int) $offset) . ',' . ((int) $limit);
        }
    }

    /**
     * order by.
     *
     * @param \Imi\Db\Query\Interfaces\IOrder[] $order
     */
    protected function parseOrder(array $order): string
    {
        if (isset($order[0]))
        {
            $params = &$this->params;
            $orderStrs = [];
            $query = $this->query;
            foreach ($order as $item)
            {
                $orderStrs[] = $item->toString($query);
                $binds = $item->getBinds();
                if ($binds)
                {
                    $params = array_merge($params, $binds);
                }
            }

            return ' order by ' . implode(',', $orderStrs);
        }
        else
        {
            return '';
        }
    }

    /**
     * group by.
     *
     * @param \Imi\Db\Query\Interfaces\IGroup[] $group
     */
    protected function parseGroup(array $group): string
    {
        if (isset($group[0]))
        {
            return ' group by ' . implode(',', $group);
        }
        else
        {
            return '';
        }
    }

    /**
     * having.
     *
     * @param \Imi\Db\Query\Interfaces\IHaving[] $having
     */
    protected function parseHaving(array $having): string
    {
        if (!$having)
        {
            return '';
        }
        $params = &$this->params;
        $query = $this->query;
        $result = [];
        foreach ($having as $item)
        {
            $result[] = $item->getLogicalOperator();
            $result[] = $item->toStringWithoutLogic($query);
            $binds = $item->getBinds();
            if ($binds)
            {
                $params = array_merge($params, $binds);
            }
        }
        unset($result[0]);
        $result = implode(' ', $result);
        if ('' !== $result)
        {
            $result = ' having ' . $result;
        }

        return $result;
    }
}
