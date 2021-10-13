<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IField;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Traits\TRaw;

class Field implements IField
{
    use TRaw;

    /**
     * 数据库名.
     */
    protected ?string $database = null;

    /**
     * 表名.
     */
    protected ?string $table = null;

    /**
     * 字段名.
     */
    protected ?string $field = null;

    /**
     * 别名.
     */
    protected ?string $alias = null;

    /**
     * JSON 关键词配置.
     */
    protected ?array $jsonKeywords = null;

    public function __construct(?string $database = null, ?string $table = null, ?string $field = null, ?string $alias = null)
    {
        $this->database = $database;
        $this->table = $table;
        $this->field = $field;
        $this->alias = $alias;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatabase(): ?string
    {
        return $this->database;
    }

    /**
     * {@inheritDoc}
     */
    public function getTable(): ?string
    {
        return $this->table;
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
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * {@inheritDoc}
     */
    public function setDatabase(?string $database = null): void
    {
        $this->database = $database;
    }

    /**
     * {@inheritDoc}
     */
    public function setTable(?string $table = null): void
    {
        $this->table = $table;
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
    public function setAlias(?string $alias = null): void
    {
        $this->alias = $alias;
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
            $this->table,
            $this->field,
        ], $this->alias, $this->jsonKeywords);
    }

    /**
     * {@inheritDoc}
     */
    public function getBinds(): array
    {
        return [];
    }
}
