<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\Builder;

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
        $setItems = [];
        foreach ($uniqueFields as &$fieldName)
        {
            $fieldName = $query->fieldQuote($fieldName);
            $setItems[] = $fieldName . ' = excluded.' . $fieldName;
        }

        return parent::build(...$args) . ' ON CONFLICT (' . implode(',', $uniqueFields) . ') DO UPDATE SET ' . implode(',', $setItems);
    }
}
