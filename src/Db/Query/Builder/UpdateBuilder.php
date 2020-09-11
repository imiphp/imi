<?php

namespace Imi\Db\Query\Builder;

class UpdateBuilder extends BaseBuilder
{
    public function build(...$args)
    {
        parent::build(...$args);
        $query = $this->query;
        $params = &$this->params;
        $option = $query->getOption();
        list($data) = $args;
        if (null === $data)
        {
            $data = $option->saveData;
        }
        $valueParams = [];
        $sql = 'update ' . $option->table . ' set ';

        // set后面的field=value
        $setStrs = [];
        foreach ($data as $k => $v)
        {
            if ($v instanceof \Imi\Db\Query\Raw)
            {
                if (is_numeric($k))
                {
                    $setStrs[] = (string) $v;
                }
                else
                {
                    $setStrs[] = $this->parseKeyword($k) . ' = ' . $v;
                }
            }
            else
            {
                $valueParam = ':' . $k;
                $valueParams[] = $valueParam;
                $params[$valueParam] = $v;
                $setStrs[] = $this->parseKeyword($k) . ' = ' . $valueParam;
            }
        }

        $sql .= implode(',', $setStrs)
            . $this->parseWhere($option->where)
            . $this->parseOrder($option->order)
            . $this->parseLimit($option->offset, $option->limit);

        $query->bindValues($params);

        return $sql;
    }
}
