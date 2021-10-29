<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IDatabase;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Traits\TRaw;

class Database implements IDatabase
{
    use TRaw;

    /**
     * 数据库名.
     */
    protected ?string $database = null;

    /**
     * 别名.
     */
    protected ?string $alias = null;

    public function __construct(?string $database = null, ?string $alias = null)
    {
        $this->database = $database;
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
            if (isset($keywords[0]))
            {
                $this->database = $keywords[0];
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
                return '(' . $this->rawSQL . ') as ' . $query->fieldQuote($this->alias ?? '');
            }
        }

        return $query->parseKeywordToText([
            $this->database,
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
