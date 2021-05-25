<?php

declare(strict_types=1);

namespace Imi\Db\Query\Builder;

class UpdateBuilder extends BaseBuilder
{
    public function build(...$args): string
    {
        parent::build(...$args);
        $query = $this->query;
        $params = &$this->params;
        $option = $query->getOption();
        list($data) = $args;
        if (null === $data)
        {
            $data = $option->saveData;
        }

        // set后面的field=value
        $setStrs = [];
        $jsonSets = [];
        foreach ($data as $k => $v)
        {
            if ($v instanceof \Imi\Db\Query\Raw)
            {
                if (is_numeric($k))
                {
                    $setStrs[] = (string) $v;
                }
                else
                {
                    $matches = $this->parseKeywordText($k);
                    $field = $this->parseKeywordToText($matches['keywords']);
                    if ($matches['jsonKeywords'])
                    {
                        $jsonSets[$field][] = [
                            'jsonKeywords' => $matches['jsonKeywords'],
                            'raw'          => $v,
                        ];
                    }
                    else
                    {
                        $setStrs[] = $field . ' = ' . $v;
                    }
                }
            }
            else
            {
                $matches = $this->parseKeywordText($k);
                $field = $this->parseKeywordToText($matches['keywords']);
                if ($matches['jsonKeywords'])
                {
                    if (is_scalar($v))
                    {
                        $valueParam = $this->query->getAutoParamName();
                        $jsonSets[$field][] = [
                            'jsonKeywords' => $matches['jsonKeywords'],
                            'valueParam'   => $valueParam,
                        ];
                        $params[$valueParam] = $v;
                    }
                    else
                    {
                        $jsonSets[$field][] = [
                            'jsonKeywords' => $matches['jsonKeywords'],
                            'raw'          => 'CONVERT(\'' . json_encode($v) . '\',JSON)',
                        ];
                    }
                }
                else
                {
                    $valueParam = ':' . $k;
                    $setStrs[] = $field . ' = ' . $valueParam;
                    $params[$valueParam] = $v;
                }
            }
        }

        $jsonSets = $this->parseJsonSet($jsonSets);
        if ($setStrs && '' !== $jsonSets)
        {
            $jsonSets = ', ' . $jsonSets;
        }
        $sql = 'update ' . $option->table . ' set ' . implode(',', $setStrs)
            . $jsonSets
            . $this->parseWhere($option->where)
            . $this->parseOrder($option->order)
            . $this->parseLimit($option->offset, $option->limit);

        $query->bindValues($params);

        return $sql;
    }

    public function parseJsonSet(array $jsonSets): string
    {
        if (!$jsonSets)
        {
            return '';
        }
        $result = [];
        foreach ($jsonSets as $field => $options)
        {
            $item = $field . '=JSON_SET(' . $field;
            foreach ($options as $option)
            {
                $item .= ',"$.' . implode('.', $option['jsonKeywords']) . '",' . ($option['raw'] ?? $option['valueParam']);
            }
            $result[] = $item . ')';
        }

        return implode(',', $result);
    }
}
