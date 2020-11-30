<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

class ReplaceBuilder extends BaseBuilder
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
        $sql = 'replace into ' . $option->table . ' ';
        if ($data instanceof \Imi\Db\Query\Interfaces\IQuery)
        {
            $builder = new SelectBuilder($data);
            $sql .= $builder->build();
            $query->bindValues($data->getBinds());
        }
        else
        {
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
                    $params[$valueParam] = $v;
                    $setStrs[] = $this->parseKeyword($k) . ' = ' . $valueParam;
                }
            }
            $sql .= 'set ' . implode(',', $setStrs);
            $query->bindValues($params);
        }

        return $sql;
    }
}
