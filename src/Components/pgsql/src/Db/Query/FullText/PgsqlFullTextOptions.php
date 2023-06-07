<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\FullText;

use Imi\Db\Query\FullText\BaseFullTextOptions;
use Imi\Db\Query\Interfaces\IQuery;

class PgsqlFullTextOptions extends BaseFullTextOptions
{
    /**
     * 分词语言
     */
    protected ?string $language = null;

    /**
     * tsquery 函数.
     */
    protected string $tsQueryFunction = TsQuery::PLAINTO_TSQUERY;

    /**
     * ts_rank 函数.
     */
    protected string $tsRankFunction = TsRank::TS_RANK_CD;

    /**
     * 设置分词语言.
     */
    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * 获取分词语言.
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * 设置 tsquery 函数.
     */
    public function setTsQueryFunction(string $tsQueryFunction): self
    {
        $this->tsQueryFunction = $tsQueryFunction;

        return $this;
    }

    /**
     * 获取 tsquery 函数.
     */
    public function getTsQueryFunction(): string
    {
        return $this->tsQueryFunction;
    }

    /**
     * 设置 ts_rank 函数.
     */
    public function setTsRankFunction(string $tsRankFunction): self
    {
        $this->tsRankFunction = $tsRankFunction;

        return $this;
    }

    /**
     * 获取 ts_rank 函数.
     */
    public function getTsRankFunction(): string
    {
        return $this->tsRankFunction;
    }

    /**
     * @return array{string[], string}
     */
    private function parseSearch(IQuery $query): array
    {
        $columns = [];
        foreach ($this->fieldNames as $name)
        {
            $columns[] = 'to_tsvector(' . $query->fieldQuote($name) . ')';
        }

        if (null === $this->language)
        {
            $tsQueryParams = '';
        }
        else
        {
            $languageParam = $query->getAutoParamName();
            $tsQueryParams = $languageParam . ',';
            $this->binds[$languageParam] = $this->language;
        }
        $searchTextParam = $query->getAutoParamName();
        $tsQueryParams .= $searchTextParam;
        $this->binds[$searchTextParam] = $this->searchText;

        return [
            $columns,
            $tsQueryParams,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function toWhereSql(IQuery $query): string
    {
        [$columns, $tsQueryParams] = $this->parseSearch($query);

        $sql = '(' . implode('||', $columns) . ') @@ ' . $this->tsQueryFunction . '(' . $tsQueryParams . ')';

        if ($this->minScore > 0)
        {
            if (null === $this->scoreFieldName)
            {
                $scoreParam = $query->getAutoParamName();
                $sql .= ' AND ' . $this->toScoreSql($query) . ' >= ' . $scoreParam;
                $this->binds[$scoreParam] = $this->minScore;
            }
            else
            {
                $sql .= ' AND ' . $query->fieldQuote($this->scoreFieldName) . ' >= ' . $query->fieldQuote($this->scoreFieldName);
            }
        }

        return $sql;
    }

    /**
     * {@inheritDoc}
     */
    public function toScoreSql(IQuery $query): string
    {
        [$columns, $tsQueryParams] = $this->parseSearch($query);

        return $this->tsRankFunction . '(' . implode('||', $columns) . ', ' . $this->tsQueryFunction . '(' . $tsQueryParams . '))';
    }
}
