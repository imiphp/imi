<?php
namespace Imi\Db\Query\Builder;

use Imi\Util\ArrayUtil;
use Imi\Db\Query\Query;
use Imi\Util\ObjectArrayHelper;


class BatchInsertBuilder extends BaseBuilder
{
    public function build(...$args)
    {
        parent::build(...$args);
        $option = $this->query->getOption();
        list($list) = $args;
        if(null === $list)
        {
            $list = $this->query->getOption()->saveData;
        }
        if($list instanceof \Traversable)
        {
            $list = \iterator_to_array($list);
        }
        if(!isset($list[0]))
        {
            throw new \RuntimeException('Batch insert must have at least 1 data');
        }
        $fields = $safeFields = [];
        foreach($list[0] as $key => $value)
        {
            $fields[] = $key;
            $safeFields[] = $this->parseKeyword($key);
        }
        $sql = 'insert into ' . $option->table . '(' . implode(',', $safeFields) . ') values ';
        $values = [];

        foreach($list as $data)
        {
            $valueParams = [];
            foreach($fields as $field)
            {
                $valueParam = $this->query->getAutoParamName();
                $valueParams[] = $valueParam;
                $this->params[$valueParam] = ObjectArrayHelper::get($data, $field);
            }
            $values[] = '(' . implode(',', $valueParams) . ')';
        }
        $sql .= implode(',', $values);
        
        $this->query->bindValues($this->params);
        return $sql;
    }
}