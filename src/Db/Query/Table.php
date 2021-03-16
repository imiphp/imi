<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\ITable;
use Imi\Db\Query\Traits\TKeyword;
use Imi\Db\Query\Traits\TRaw;

class Table implements ITable
{
    use TKeyword;
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
     * 获取数据库名.
     */
    public function getDatabase(): ?string
    {
        return $this->database;
    }

    /**
     * 获取表名.
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * 获取别名.
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * 设置数据库名.
     */
    public function setDatabase(?string $database = null): void
    {
        $this->database = $database;
    }

    /**
     * 设置表名.
     */
    public function setTable(?string $table = null): void
    {
        $this->table = $table;
    }

    /**
     * 设置别名.
     */
    public function setAlias(?string $alias = null): void
    {
        $this->alias = $alias;
    }

    /**
     * 设置值，可以根据传入的值自动处理
     * name——table
     * parent.name——database.table
     * name alias——table alias
     * name as alias—— table as alias.
     */
    public function setValue(string $value): void
    {
        $matches = $this->parseKeywordText($value);
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

    public function __toString()
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }

        return $this->parseKeywordToText([
            $this->database,
            $this->table,
        ], $this->alias);
    }

    /**
     * 获取绑定的数据们.
     */
    public function getBinds(): array
    {
        return [];
    }
}
