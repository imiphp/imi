<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\Builder;

use Imi\Db\Query\Field;

abstract class BaseBuilder extends \Imi\Db\Query\Builder\BaseBuilder
{
    /**
     * fields.
     */
    protected function parseField(array $fields): string
    {
        if (!$fields)
        {
            return '*';
        }
        $result = [];
        $query = $this->query;
        $params = &$this->params;
        foreach ($fields as $k => $v)
        {
            if (\is_int($k))
            {
                if ($v instanceof Field)
                {
                    $field = $v;
                }
                else
                {
                    $field = new Field();
                    $field->setValue($v ?? '', $query);
                }
            }
            else
            {
                $field = new Field(null, null, $k, $v);
            }
            $result[] = $field->toString($query);
            $binds = $field->getBinds();
            if ($binds)
            {
                $params = array_merge($params, $binds);
            }
        }

        return implode(',', $result);
    }

    /**
     * limit.
     */
    protected function parseLimit(?int $offset, ?int $limit): string
    {
        if (null === $limit)
        {
            return '';
        }
        elseif (null === $offset)
        {
            $sql = ' limit ' . ($limitName = $this->query->getAutoParamName());

            $this->params[$limitName] = (int) $limit;

            return $sql;
        }
        else
        {
            $sql = ' limit ' . ($limitName = $this->query->getAutoParamName()) . ' offset ' . ($offsetName = $this->query->getAutoParamName());

            $this->params[$offsetName] = (int) $offset;
            $this->params[$limitName] = (int) $limit;

            return $sql;
        }
    }
}
