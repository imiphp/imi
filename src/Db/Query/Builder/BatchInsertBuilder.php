<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

use Imi\Util\ObjectArrayHelper;

class BatchInsertBuilder extends BaseBuilder
{
    public function build(...$args)
    {
        parent::build(...$args);
        $query = $this->query;
        $params = &$this->params;
        $option = $query->getOption();
        list($list) = $args;
        if (null === $list)
        {
            $list = $option->saveData;
        }
        if ($list instanceof \Traversable)
        {
            $list = iterator_to_array($list);
        }
        if (!isset($list[0]))
        {
            throw new \RuntimeException('Batch insert must have at least 1 data');
        }
        $fields = array_keys($list[0]);
        $safeFields = [];
        foreach ($fields as $key)
        {
            $safeFields[] = $this->parseKeyword($key);
        }
        $sql = 'insert into ' . $option->table . '(' . implode(',', $safeFields) . ') values ';
        $values = [];

        foreach ($list as $data)
        {
            $valueParams = [];
            foreach ($fields as $field)
            {
                $valueParam = $query->getAutoParamName();
                $valueParams[] = $valueParam;
                $params[$valueParam] = ObjectArrayHelper::get($data, $field);
            }
            $values[] = '(' . implode(',', $valueParams) . ')';
        }
        $sql .= implode(',', $values);

        $query->bindValues($params);

        return $sql;
    }
}
