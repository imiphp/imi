<?php
namespace Imi\Db\Query\Builder;

use Imi\Db\Query\Query;
use Imi\Util\ArrayUtil;


class ReplaceBuilder extends BaseBuilder
{
    public function build(...$args)
    {
        $option = $this->query->getOption();
        list($data) = $args;
        if(null === $data)
        {
            $data = $this->query->getOption()->saveData;
        }
        $sql = 'replace into ' . $option->table . ' ';
        if($data instanceof \Imi\Db\Query\Interfaces\IQuery)
        {
            $builder = new SelectBuilder($data);
            $sql .= $builder->build();
            $this->query->bindValues($data->getBinds());
        }
        else
        {
            // set后面的field=value
            $setStrs = [];
            foreach($data as $k => $v)
            {
                if($v instanceof \Imi\Db\Query\Raw)
                {
                    if(is_numeric($k))
                    {
                        $setStrs[] = (string)$v;
                    }
                    else
                    {
                        $setStrs[] = $this->parseKeyword($k) . ' = ' . $v;
                    }
                }
                else
                {
                    $valueParam = Query::getAutoParamName();
                    $valueParams[] = $valueParam;
                    $this->params[$valueParam] = $v;
                    $setStrs[] = $this->parseKeyword($k) . ' = ' . $valueParam;
                }
            }
            $sql .= 'set ' . implode(',', $setStrs);
            $this->query->bindValues($this->params);
        }
        return $sql;
    }
}