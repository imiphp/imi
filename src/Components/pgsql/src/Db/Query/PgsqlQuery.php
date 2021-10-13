<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query;

use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\Query;
use Imi\Db\Query\QueryType;
use Imi\Pgsql\Db\Query\Builder\BatchInsertBuilder;
use Imi\Pgsql\Db\Query\Builder\DeleteBuilder;
use Imi\Pgsql\Db\Query\Builder\InsertBuilder;
use Imi\Pgsql\Db\Query\Builder\ReplaceBuilder;
use Imi\Pgsql\Db\Query\Builder\SelectBuilder;
use Imi\Pgsql\Db\Query\Builder\UpdateBuilder;
use Imi\Util\Text;

class PgsqlQuery extends Query
{
    /**
     * {@inheritDoc}
     */
    public function select(): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new SelectBuilder($this);
            $sql = $builder->build();
            if ($alias)
            {
                // @phpstan-ignore-next-line
                $originBinds = $binds;
                $binds = $this->binds;
                if ($binds)
                {
                    $this->binds = array_merge($originBinds, $binds);
                }
                else
                {
                    $this->binds = $originBinds;
                }
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        }
        if (!$this->isInitQueryType && !$this->isInTransaction())
        {
            $this->queryType = QueryType::READ;
        }

        return $this->execute($sql);
    }

    /**
     * {@inheritDoc}
     */
    public function insert($data = null): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
            $bindValues = [];
            $numberKey = isset($data[0]);
            foreach ($data as $k => $v)
            {
                if ($numberKey)
                {
                    $bindValues[':' . ($k + 1)] = $v;
                }
                else
                {
                    $bindValues[':' . $k] = $v;
                }
            }
            $this->bindValues($bindValues);
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new InsertBuilder($this);
            $sql = $builder->build($data);
            if ($alias)
            {
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    // @phpstan-ignore-next-line
                    'binds' => $binds,
                ];
            }
        }

        return $this->execute($sql);
    }

    /**
     * {@inheritDoc}
     */
    public function batchInsert($data = null): IResult
    {
        $builder = new BatchInsertBuilder($this);
        $sql = $builder->build($data);

        return $this->execute($sql);
    }

    /**
     * {@inheritDoc}
     */
    public function update($data = null): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
            $bindValues = [];
            foreach ($data as $k => $v)
            {
                $bindValues[':' . $k] = $v;
            }
            $this->bindValues($bindValues);
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new UpdateBuilder($this);
            $sql = $builder->build($data);
            if ($alias)
            {
                // @phpstan-ignore-next-line
                $originBinds = $binds;
                $binds = $this->binds;
                if ($binds)
                {
                    $this->binds = array_merge($originBinds, $binds);
                }
                else
                {
                    $this->binds = $originBinds;
                }
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        }

        return $this->execute($sql);
    }

    /**
     * {@inheritDoc}
     */
    public function replace($data = null): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
            $bindValues = [];
            foreach ($data as $k => $v)
            {
                $bindValues[':' . $k] = $v;
            }
            $this->bindValues($bindValues);
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new ReplaceBuilder($this);
            $sql = $builder->build($data);
            if ($alias)
            {
                // @phpstan-ignore-next-line
                $originBinds = $binds;
                $binds = $this->binds;
                if ($binds)
                {
                    $this->binds = array_merge($originBinds, $binds);
                }
                else
                {
                    $this->binds = $originBinds;
                }
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        }

        return $this->execute($sql);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): IResult
    {
        $alias = $this->alias;
        $aliasSqlMap = &static::$aliasSqlMap;
        if ($alias && isset($aliasSqlMap[$alias]))
        {
            $aliasSqlData = $aliasSqlMap[$alias];
            $sql = $aliasSqlData['sql'];
            $binds = $aliasSqlData['binds'];
            if ($binds)
            {
                if ($this->binds)
                {
                    $this->binds = array_merge($binds, $this->binds);
                }
                else
                {
                    $this->binds = $binds;
                }
            }
        }
        else
        {
            if ($alias)
            {
                $binds = $this->binds;
                $this->binds = [];
            }
            $builder = new DeleteBuilder($this);
            $sql = $builder->build();
            if ($alias)
            {
                // @phpstan-ignore-next-line
                $originBinds = $binds;
                $binds = $this->binds;
                if ($binds)
                {
                    $this->binds = array_merge($originBinds, $binds);
                }
                else
                {
                    $this->binds = $originBinds;
                }
                $aliasSqlMap[$alias] = [
                    'sql'   => $sql,
                    'binds' => $binds,
                ];
            }
        }
        $result = $this->execute($sql);

        return $result;
    }

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
