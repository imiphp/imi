<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IWrapField;

class WrapField extends Field implements IWrapField
{
    public function __construct(protected string $wrapLeft, protected array $subFields, protected string $wrapRight, ?string $alias = null)
    {
        $this->alias = $alias;
    }

    public function getSubFields(): array
    {
        return $this->subFields;
    }

    public function setSubFields(array $subFields): void
    {
        $this->subFields = $subFields;
    }

    public function getWrapLeft(): string
    {
        return $this->wrapLeft;
    }

    public function setWrapLeft(string $wrapLeft): void
    {
        $this->wrapLeft = $wrapLeft;
    }

    public function getWrapRight(): string
    {
        return $this->wrapRight;
    }

    public function setWrapRight(string $wrapRight): void
    {
        $this->wrapRight = $wrapRight;
    }

    /**
     * {@inheritDoc}
     */
    public function toString(IQuery $query): string
    {
        if (null === $this->alias)
        {
            $alias = '';
        }
        else
        {
            $alias = ' as ' . $query->fieldQuote($this->alias);
        }
        if ($this->subFields)
        {
            $result = [];
            $binds = &$this->binds;
            foreach ($this->subFields as $k => $v)
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
                $fieldBinds = $field->getBinds();
                if ($fieldBinds)
                {
                    $binds = array_merge($binds, $fieldBinds);
                }
            }

            $result = implode(',', $result);
        }
        else
        {
            $result = parent::toString($query);
        }

        return $this->wrapLeft . $result . $this->wrapRight . $alias;
    }
}
