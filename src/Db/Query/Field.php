<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IField;
use Imi\Db\Query\Interfaces\IQuery;

class Field extends Table implements IField
{
    /**
     * 字段名.
     */
    protected ?string $field = null;

    /**
     * JSON 关键词配置.
     */
    protected ?array $jsonKeywords = null;

    public function __construct(?string $database = null, ?string $table = null, ?string $field = null, ?string $alias = null, string $prefix = '')
    {
        $this->database = $database;
        $this->table = $table;
        $this->field = $field;
        $this->alias = $alias;
        $this->prefix = $prefix;
    }

    /**
     * {@inheritDoc}
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * {@inheritDoc}
     */
    public function setField(?string $field = null): void
    {
        $this->field = $field;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue(string $value, IQuery $query): void
    {
        $matches = $query->parseKeywordText($value);
        if (isset($matches['keywords']))
        {
            $keywords = $matches['keywords'];
            if (isset($keywords[2]))
            {
                $this->database = $keywords[0];
                $this->table = $keywords[1];
                $this->field = $keywords[2];
            }
            elseif (isset($keywords[1]))
            {
                $this->database = null;
                $this->table = $keywords[0];
                $this->field = $keywords[1];
            }
            elseif (isset($keywords[0]))
            {
                $this->database = null;
                $this->table = null;
                $this->field = $keywords[0];
            }
            $this->alias = $matches['alias'];
            $this->jsonKeywords = $matches['jsonKeywords'];
            $this->prefix = '';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function toString(IQuery $query): string
    {
        if ($this->isRaw)
        {
            if (null === $this->alias)
            {
                return $this->rawSQL;
            }
            else
            {
                return '(' . $this->rawSQL . ') as ' . $query->fieldQuote($this->alias);
            }
        }

        return $query->parseKeywordToText([
            $this->database,
            $this->prefix . $this->table,
            $this->field,
        ], $this->alias, $this->jsonKeywords);
    }
}
