<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\ITable;
use Imi\Db\Query\Traits\TRaw;

class Table implements ITable
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
     * 别名.
     */
    protected ?string $alias = null;

    public function __construct(?string $database = null, ?string $table = null, ?string $alias = null)
    {
        $this->database = $database;
        $this->table = $table;
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
            if (isset($keywords[1]))
            {
                $this->database = $keywords[0];
                $this->table = $keywords[1];
            }
            elseif (isset($keywords[0]))
            {
                $this->database = null;
                $this->table = $keywords[0];
            }
            $this->alias = $matches['alias'];
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
        ], $this->alias);
    }

    /**
     * {@inheritDoc}
     */
    public function getBinds(): array
    {
        return [];
    }
}
