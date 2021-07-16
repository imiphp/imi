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
     * 获取字段名.
     */
    public function getField(): ?string
    {
        return $this->field;
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
     * 设置字段名.
     */
    public function setField(?string $field = null): void
    {
        $this->field = $field;
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
     * name——field
     * parent.name——table.field
     * parent.parent.name——database.table.field
     * name alias——field alias
     * name as alias—— field as alias.
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
     * 获取绑定的数据们.
     */
    public function getBinds(): array
    {
        return [];
    }
}
