<?php

namespace Imi\Db\Query;

use Imi\Db\Query\Builder\BaseBuilder;
use Imi\Db\Query\Interfaces\ITable;
use Imi\Db\Query\Traits\TKeyword;
use Imi\Db\Query\Traits\TRaw;

class Table implements ITable
{
    use TKeyword;
    use TRaw;

    /**
     * 数据库名.
     *
     * @var string|null
     */
    protected $database;

    /**
     * 表名.
     *
     * @var string|null
     */
    protected $table;

    /**
     * 别名.
     *
     * @var string|null
     */
    protected $alias;

    public function __construct(?string $database = null, ?string $table = null, ?string $alias = null)
    {
        $this->database = $database;
        $this->table = $table;
        $this->alias = $alias;
    }

    /**
     * 获取数据库名.
     *
     * @return string|null
     */
    public function getDatabase(): ?string
    {
        return $this->database;
    }

    /**
     * 获取表名.
     *
     * @return string|null
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * 获取别名.
     *
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * 设置数据库名.
     *
     * @param string $database
     *
     * @return void
     */
    public function setDatabase(string $database = null)
    {
        $this->database = $database;
    }

    /**
     * 设置表名.
     *
     * @param string $table
     *
     * @return void
     */
    public function setTable(string $table = null)
    {
        $this->table = $table;
    }

    /**
     * 设置别名.
     *
     * @param string $alias
     *
     * @return void
     */
    public function setAlias(string $alias = null)
    {
        $this->alias = $alias;
    }

    /**
     * 设置值，可以根据传入的值自动处理
     * name——table
     * parent.name——database.table
     * name alias——table alias
     * name as alias—— table as alias.
     *
     * @param string|null $value
     *
     * @return void
     */
    public function setValue($value)
    {
        $matches = $this->parseKeywordText($value ?? '');
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
            if (null === $this->alias)
            {
                return $this->rawSQL;
            }
            else
            {
                return '(' . $this->rawSQL . ') as ' . BaseBuilder::DELIMITED_IDENTIFIERS . $this->alias . BaseBuilder::DELIMITED_IDENTIFIERS;
            }
        }

        return $this->parseKeywordToText([
            $this->database,
            $this->table,
        ], $this->alias);
    }

    /**
     * 获取绑定的数据们.
     *
     * @return array
     */
    public function getBinds()
    {
        return [];
    }
}
