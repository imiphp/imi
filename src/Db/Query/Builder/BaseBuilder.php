<?php
namespace Imi\Db\Query\Builder;

use Imi\Db\Query\Field;
use Imi\Db\Query\Query;
use Imi\Db\Traits\SqlParser;
use Imi\Db\Query\Traits\TKeyword;
use Imi\Db\Query\Interfaces\IQuery;

abstract class BaseBuilder implements IBuilder
{
    use TKeyword;
    use SqlParser;
    
    /**
     * 分隔标识符，解决保留字问题
     */
    const DELIMITED_IDENTIFIERS = '`';
    
    /**
     * IQuery 类
     * @var \Imi\Db\Query\Interfaces\IQuery
     */
    protected $query;

    /**
     * 绑定参数
     * @var array
     */
    protected $params = [];
    
    public function __construct(IQuery $query)
    {
        $this->query = $query;
    }
    
    /**
     * distinct
     * @param boolean $distinct
     * @return string
     */
    protected function parseDistinct(bool $distinct)
    {
        return $distinct ? 'distinct ' : '';
    }

    /**
     * fields
     * @param array $fields
     * @return string
     */
    protected function parseField($fields)
    {
        if(!isset($fields[0]))
        {
            return '*';
        }
        $result = [];
        foreach($fields as $k => $v)
        {
            if(is_numeric($k))
            {
                if($v instanceof Field)
                {
                    $field = $v;
                }
                else
                {
                    $field = new Field;
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
     * join
     * @param \Imi\Db\Query\Interfaces\IJoin[] $join
     * @return string
     */
    protected function parseJoin($join)
    {
        $result = implode(' ', $join);
        foreach($join as $item)
        {
            $this->params = array_merge($this->params, $item->getBinds());
        }
        return $result;
    }

    /**
     * where
     * @param \Imi\Db\Query\Interfaces\IBaseWhere[] $where
     * @return string
     */
    protected function parseWhere($where)
    {
        $result = [];
        foreach($where as $item)
        {
            $result[] = $item->getLogicalOperator();
            $result[] = $item->toStringWithoutLogic();
            $this->params = array_merge($this->params, $item->getBinds());
        }
        array_shift($result);
        $result = implode(' ', $result);
        if('' !== $result)
        {
            $result = ' where ' . $result;
        }
        return $result;
    }

    /**
     * limit
     * @param int $offset
     * @param int $limit
     * @return string
     */
    protected function parseLimit($offset, $limit)
    {
        if(null === $limit)
        {
            return '';
        }
        else if(null === $offset)
        {
            $limitName = Query::getAutoParamName();
            $this->params[$limitName] = $limit;
            return ' limit ' . $limitName;
        }
        else
        {
            $offsetName = Query::getAutoParamName();
            $this->params[$offsetName] = $offset;
            $limitName = Query::getAutoParamName();
            $this->params[$limitName] = $limit;
            return ' limit ' . $offsetName . ',' . $limitName;
        }
    }

    /**
     * order by
     * @param \Imi\Db\Query\Interfaces\IOrder[] $order
     * @return string
     */
    protected function parseOrder($order)
    {
        if(isset($order[0]))
        {
            $result = ' order by ' . implode(',', $order);
            foreach($order as $item)
            {
                $this->params = array_merge($this->params, $item->getBinds());
            }
            return $result;
        }
        else
        {
            return '';
        }
    }

    /**
     * group by
     * @param \Imi\Db\Query\Interfaces\IGroup[] $group
     * @return string
     */
    protected function parseGroup($group)
    {
        if(isset($group[0]))
        {
            return ' group by ' . implode(',', $group);
        }
        else
        {
            return '';
        }
    }

    /**
     * having
     * @param \Imi\Db\Query\Interfaces\IHaving[] $having
     * @return string
     */
    protected function parseHaving($having)
    {
        $result = [];
        foreach($having as $item)
        {
            $result[] = $item->getLogicalOperator();
            $result[] = $item->toStringWithoutLogic();
            $this->params = array_merge($this->params, $item->getBinds());
        }
        array_shift($result);
        $result = implode(' ', $result);
        if('' !== $result)
        {
            $result = ' having ' . $result;
        }
        return $result;
    }
}