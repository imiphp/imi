<?php

namespace Imi\Db\Query;

use Imi\Db\Query\Interfaces\IDatabase;
use Imi\Db\Query\Traits\TKeyword;
use Imi\Db\Query\Traits\TRaw;

class Database implements IDatabase
{
    use TRaw;
    use TKeyword;

    /**
     * 数据库名.
     *
     * @var string|null
     */
    protected $database;

    /**
     * 别名.
     *
     * @var string|null
     */
    protected $alias;

    public function __construct(?string $database = null, ?string $alias = null)
    {
        $this->database = $database;
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
     * @param string|null $database
     *
     * @return void
     */
    public function setDatabase(?string $database = null)
    {
        $this->database = $database;
    }

    /**
     * 设置别名.
     *
     * @param string|null $alias
     *
     * @return void
     */
    public function setAlias(?string $alias = null)
    {
        $this->alias = $alias;
    }

    /**
     * 设置值，可以根据传入的值自动处理
     * name——database
     * name alias——database alias
     * name as alias——database as alias.
     *
     * @param string $value
     *
     * @return void
     */
    public function setValue($value)
    {
        $matches = $this->parseKeywordText($value);
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

    public function __toString()
    {
        if ($this->isRaw)
        {
            return $this->rawSQL;
        }

        return $this->parseKeywordToText([
            $this->database,
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
