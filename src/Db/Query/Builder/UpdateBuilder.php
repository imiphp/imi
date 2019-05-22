<?php
namespace Imi\Db\Query\Builder;

use Imi\Db\Query\Query;

class UpdateBuilder extends BaseBuilder
{
    public function build(...$args)
    {
        parent::build(...$args);
        $option = $this->query->getOption();
        list($data) = $args;
        if(null === $data)
        {
            $data = $this->query->getOption()->saveData;
        }
        $valueParams = [];
        $sql = 'update ' . $option->table . ' set ';

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

        $sql .= implode(',', $setStrs)
            . $this->parseWhere($option->where)
            . $this->parseOrder($option->order)
            . $this->parseLimit($option->offset, $option->limit);

        $this->query->bindValues($this->params);
        return $sql;
    }
}