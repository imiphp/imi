<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Query\FullText;

use Imi\Db\Query\FullText\BaseFullTextOptions;
use Imi\Db\Query\Interfaces\IQuery;

class MysqlFullTextOptions extends BaseFullTextOptions
{
    /**
     * 搜索修饰符.
     */
    protected string $searchModifier = '';

    public function __construct(string $searchModifier = '')
    {
        $this->searchModifier = $searchModifier;
    }

    /**
     * 设置搜索修饰符.
     */
    public function setSearchModifier(string $searchModifier): self
    {
        $this->searchModifier = $searchModifier;

        return $this;
    }

    /**
     * 获取搜索修饰符.
     */
    public function getSearchModifier(): string
    {
        return $this->searchModifier;
    }

    private function toMatchSql(IQuery $query): string
    {
        $searchTextParam = $query->getAutoParamName();
        $fieldNames = [];
        foreach ($this->fieldNames as $name)
        {
            $fieldNames[] = $query->fieldQuote($name);
        }
        $searchModifier = $this->searchModifier;
        $sql = 'MATCH (' . implode(',', $fieldNames) . ') AGAINST (' . $searchTextParam . ('' === $searchModifier ? '' : (' ' . $searchModifier)) . ')';
        $this->binds[$searchTextParam] = $this->searchText;

        return $sql;
    }

    /**
     * {@inheritDoc}
     */
    public function toWhereSql(IQuery $query): string
    {
        $sql = $this->toMatchSql($query);
        $scoreConditionParam = $query->getAutoParamName();
        $this->binds[$scoreConditionParam] = $this->minScore;
        $sql .= ' > ' . $scoreConditionParam;

        return $sql;
    }

    /**
     * {@inheritDoc}
     */
    public function toScoreSql(IQuery $query): string
    {
        return $this->toMatchSql($query);
    }
}
