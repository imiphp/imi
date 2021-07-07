<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

use Imi\Db\Query\Field;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Traits\TKeyword;

abstract class BaseBuilder implements IBuilder
{
    use TKeyword;

    /**
     * 分隔标识符，解决保留字问题.
     */
    public const DELIMITED_IDENTIFIERS = '`';

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
    protected function parseField(array $fields): string
    {
        if (!isset($fields[0]))
        {
            return '*';
        }
        $result = [];
        foreach ($fields as $k => $v)
        {
            if (is_numeric($k))
            {
                if ($v instanceof Field)
                {
                    $field = $v;
                }
                else
                {
                    $field = new Field();
                    $field->setValue($v);
                }
            }
            else
            {
                $field = new Field(null, null, $k, $v);
            }
            $result[] = $field;
        }

        return implode(',', $result);
    }

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
            $result = ' order by ' . implode(',', $order);
            $params = &$this->params;
            foreach ($order as $item)
            {
                $binds = $item->getBinds();
                if ($binds)
                {
                    $params = array_merge($params, $binds);
                }
            }

            return $result;
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
