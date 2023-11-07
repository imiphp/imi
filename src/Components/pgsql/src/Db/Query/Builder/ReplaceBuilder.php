<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\Builder;

use Imi\Util\ArrayUtil;

class ReplaceBuilder extends InsertBuilder
{
    public function build(mixed ...$args): string
    {
        [, $uniqueFields] = $args;
        if (!$uniqueFields)
        {
            throw new \InvalidArgumentException('pgsql replace must set unique fields');
        }
        $query = $this->query;
        [$data] = $args;
        if (null === $data)
        {
            $data = $query->getOption()->saveData;
        }
        if ($data instanceof \Traversable)
        {
            $data = iterator_to_array($data);
        }
        if (ArrayUtil::isAssoc($data))
        {
            $setItems = [];
            foreach ($data as $k => $_)
            {
                if (!\in_array($k, $uniqueFields))
                {
                    $fieldName = $query->fieldQuote($k);
                    $setItems[] = $fieldName . ' = excluded.' . $fieldName;
                }
            }
        }
        else
        {
            throw new \InvalidArgumentException('replace() only supports key-value arrays');
        }
        foreach ($uniqueFields as &$fieldName)
        {
            $fieldName = $query->fieldQuote($fieldName);
        }

        return parent::build(...$args) . ' ON CONFLICT (' . implode(',', $uniqueFields) . ') DO UPDATE SET ' . implode(',', $setItems);
    }
}
