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
        foreach ($fields as $k => $v)
        {
            if (is_numeric($k))
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
            return ' limit ' . ((int) $limit);
        }
        else
        {
            return ' limit ' . ((int) $limit) . ' offset ' . ((int) $offset);
        }
    }
}
