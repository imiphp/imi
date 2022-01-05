<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\Builder;

use Imi\Db\Query\QueryOption;

class ReplaceBuilder extends BaseBuilder
{
    public function build(...$args): string
    {
        parent::build(...$args);
        $query = $this->query;
        $params = &$this->params;
        /** @var QueryOption $option */
        $option = $query->getOption();
        [$data] = $args;
        if (null === $data)
        {
            $data = $option->saveData;
        }
        $sql = 'replace into ' . $option->table->toString($query) . ' ';
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
                        $setStrs[] = $v->toString($query);
                    }
                    else
                    {
                        $setStrs[] = $query->fieldQuote($k) . ' = ' . $v->toString($query);
                    }
                }
                else
                {
                    $valueParam = ':' . $k;
                    $params[$valueParam] = $v;
                    $setStrs[] = $query->fieldQuote($k) . ' = ' . $valueParam;
                }
            }
            $sql .= 'set ' . implode(',', $setStrs);
            $query->bindValues($params);
        }

        return $sql;
    }
}
