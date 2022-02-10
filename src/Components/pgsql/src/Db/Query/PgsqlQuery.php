<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query;

use Imi\Db\Query\Query;
use Imi\Pgsql\Db\Query\Builder\BatchInsertBuilder;
use Imi\Pgsql\Db\Query\Builder\DeleteBuilder;
use Imi\Pgsql\Db\Query\Builder\InsertBuilder;
use Imi\Pgsql\Db\Query\Builder\ReplaceBuilder;
use Imi\Pgsql\Db\Query\Builder\SelectBuilder;
use Imi\Pgsql\Db\Query\Builder\UpdateBuilder;
use Imi\Util\Text;

class PgsqlQuery extends Query
{
    public const SELECT_BUILDER_CLASS = SelectBuilder::class;

    public const INSERT_BUILDER_CLASS = InsertBuilder::class;

    public const BATCH_INSERT_BUILDER_CLASS = BatchInsertBuilder::class;

    public const UPDATE_BUILDER_CLASS = UpdateBuilder::class;

    public const REPLACE_BUILDER_CLASS = ReplaceBuilder::class;

    public const DELETE_BUILDER_CLASS = DeleteBuilder::class;

    /**
     * {@inheritDoc}
     */
    public function fieldQuote(string $name): string
    {
        $matches = $this->parseKeywordText($name);

        return $this->parseKeywordToText($matches['keywords'], $matches['alias'], $matches['jsonKeywords']);
    }

    /**
     * {@inheritDoc}
     */
    public function parseKeywordText(string $string): array
    {
        $split = explode('->', $string);
        static $pattern = '/(?P<keywords>[^\s\.]+)(\s+(?:as\s+)?(?P<alias>.+))?/';
        if (preg_match_all($pattern, str_replace('"', '', $split[0]), $matches) > 0)
        {
            if (isset($split[1]))
            {
                if (preg_match_all($pattern, str_replace('"', '', $split[1]), $matches2) > 0)
                {
                    $alias = end($matches2['alias']);
                    if (!$alias)
                    {
                        $alias = null;
                    }

                    return [
                        'keywords'      => $matches['keywords'],
                        'alias'         => $alias,
                        'jsonKeywords'  => $matches2['keywords'] ?? null,
                    ];
                }
            }
            else
            {
                $alias = end($matches['alias']);
                if (!$alias)
                {
                    $alias = null;
                }

                return [
                    'keywords'      => $matches['keywords'],
                    'alias'         => $alias,
                    'jsonKeywords'  => null,
                ];
            }
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function parseKeywordToText(array $keywords, ?string $alias = null, ?array $jsonKeywords = null): string
    {
        foreach ($keywords as $k => $v)
        {
            if (Text::isEmpty($v))
            {
                unset($keywords[$k]);
            }
        }
        $isLastStar = '*' === end($keywords);
        $result = '"' . implode('"' . '.' . '"', $keywords) . '"';
        if ($isLastStar)
        {
            $result = str_replace('"' . '*' . '"', '*', $result);
        }
        if (null !== $jsonKeywords)
        {
            $result = '(' . $result . ' #>> \'{' . $this->parseToJsonKeywordsStr($jsonKeywords) . '}\')';
        }
        if (!Text::isEmpty($alias))
        {
            $result .= ' as ' . '"' . $alias . '"';
        }

        return $result;
    }

    public function parseToJsonKeywordsStr(array $jsonKeywords): string
    {
        return str_replace(['[', '].', ']'], [',', ',', ''], implode(',', $jsonKeywords));
    }
}
