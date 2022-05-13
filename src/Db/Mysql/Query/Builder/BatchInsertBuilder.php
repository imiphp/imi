<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Query\Builder;

use Imi\Db\Query\QueryOption;
use Imi\Util\ObjectArrayHelper;

class BatchInsertBuilder extends BaseBuilder
{
    public function build(...$args): string
    {
        parent::build(...$args);
        $query = $this->query;
        $params = &$this->params;
        /** @var QueryOption $option */
        $option = $query->getOption();
        [$list] = $args;
        if (null === $list)
        {
            $list = $option->saveData;
        }
        if ($list instanceof \Traversable)
        {
            $list = iterator_to_array($list);
        }
        if (!$list)
        {
            throw new \RuntimeException('Batch insert must have at least 1 data');
        }
        $fields = array_keys(reset($list));
        $safeFields = [];
        foreach ($fields as $key)
        {
            $safeFields[] = $query->fieldQuote($key);
        }
        $sql = 'insert into ' . $option->table->toString($query) . '(' . implode(',', $safeFields) . ') values ';
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
