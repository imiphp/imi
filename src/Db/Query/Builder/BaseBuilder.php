<?php

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
    const DELIMITED_IDENTIFIERS = '`';

    /**
     * IQuery 类.
     *
     * @var \Imi\Db\Query\Interfaces\IQuery
     */
    protected $query;

    /**
     * 绑定参数.
     *
     * @var array
     */
    protected $params = [];

    /**
     * 生成SQL语句.
     *
     * @param IQuery $query
     * @param mixed  $args
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
     *
     * @return string
     */
    public function build(...$args)
    {
        $this->params = [];

        return '';
    }

    /**
     * distinct.
     *
     * @param bool $distinct
     *
     * @return string
     */
    protected function parseDistinct(bool $distinct)
    {
        return $distinct ? 'distinct ' : '';
    }

    /**
     * fields.
     *
     * @param array $fields
     *
     * @return string
     */
    protected function parseField($fields)
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
     *
     * @return string
     */
    protected function parseJoin($join)
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
     *
     * @return string
     */
    protected function parseWhere($where)
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
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return string
     */
    protected function parseLimit($offset, $limit)
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
     *
     * @return string
     */
    protected function parseOrder($order)
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
     *
     * @return string
     */
    protected function parseGroup($group)
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
     *
     * @return string
     */
    protected function parseHaving($having)
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
