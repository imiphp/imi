<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Query\Builder;

use Imi\Db\Query\QueryOption;
use Imi\Util\ArrayUtil;

class InsertBuilder extends BaseBuilder
{
    public function build(mixed ...$args): string
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
        if ($data instanceof \Traversable)
        {
            $data = iterator_to_array($data);
        }
        $valueParams = [];
        $ignore = ($option->options['ignore'] ?? false) ? ' ignore' : '';
        if (ArrayUtil::isAssoc($data))
        {
            $fields = [];
            // 键值数组
            foreach ($data as $k => $v)
            {
                if ($v instanceof \Imi\Db\Query\Raw)
                {
                    if (!\is_int($k))
                    {
                        $fields[] = $query->fieldQuote($k);
                        $valueParams[] = $v->toString($query);
                    }
                    $binds = $v->getBinds();
                    if ($binds)
                    {
                        $params = array_merge($params, $binds);
                    }
                }
                else
                {
                    $fields[] = $query->fieldQuote($k);
                    $valueParam = ':' . $k;
                    $valueParams[] = $valueParam;
                    $params[$valueParam] = $v;
                }
            }
            $sql = "insert{$ignore} into " . $option->table->toString($query)
                . (($option->partition && '' !== ($partition = $option->partition->toString($query))) ? (' PARTITION(' . $partition . ')') : '')
                . ' (' . implode(',', $fields) . ') values(' . implode(',', $valueParams) . ')';
        }
        else
        {
            // 普通数组
            foreach ($data as $v)
            {
                if ($v instanceof \Imi\Db\Query\Raw)
                {
                    $valueParams[] = $v->toString($query);
                }
                else
                {
                    $valueParam = $query->getAutoParamName();
                    $valueParams[] = $valueParam;
                    $params[$valueParam] = $v;
                }
            }
            $sql = "insert{$ignore} into " . $option->table->toString($query) . ' values(' . implode(',', $valueParams) . ')';
        }
        $query->bindValues($params);

        return $sql;
    }
}
