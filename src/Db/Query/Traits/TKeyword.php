<?php

declare(strict_types=1);

namespace Imi\Db\Query\Traits;

use Imi\Db\Query\Builder\BaseBuilder;
use Imi\Util\Text;

trait TKeyword
{
    /**
     * 把输入的关键字文本转为数组.
     *
     * @param string $string
     *
     * @return array
     */
    public function parseKeywordText(string $string): array
    {
        $split = explode('->', $string);
        static $pattern = '/(?P<keywords>[^\s\.]+)(\s+(?:as\s+)?(?P<alias>.+))?/';
        if (preg_match_all($pattern, str_replace(BaseBuilder::DELIMITED_IDENTIFIERS, '', $split[0]), $matches) > 0)
        {
            if (isset($split[1]))
            {
                if (preg_match_all($pattern, str_replace(BaseBuilder::DELIMITED_IDENTIFIERS, '', $split[1]), $matches2) > 0)
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
                    'jsonKeywords'  => $matches['jsonKeywords'] ?? null,
                ];
            }
        }

        return [];
    }

    /**
     * 从数组拼装为有分隔标识符的关键字.
     *
     * @param array       $keywords
     * @param string|null $alias
     * @param array|null  $jsonKeywords
     *
     * @return string
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
        $result = BaseBuilder::DELIMITED_IDENTIFIERS . implode(BaseBuilder::DELIMITED_IDENTIFIERS . '.' . BaseBuilder::DELIMITED_IDENTIFIERS, $keywords) . BaseBuilder::DELIMITED_IDENTIFIERS;
        if ($isLastStar)
        {
            $result = str_replace(BaseBuilder::DELIMITED_IDENTIFIERS . '*' . BaseBuilder::DELIMITED_IDENTIFIERS, '*', $result);
        }
        if (null !== $jsonKeywords)
        {
            $result .= '->"$.' . implode('.', $jsonKeywords) . '"';
        }
        if (!Text::isEmpty($alias))
        {
            $result .= ' as ' . BaseBuilder::DELIMITED_IDENTIFIERS . $alias . BaseBuilder::DELIMITED_IDENTIFIERS;
        }

        return $result;
    }

    /**
     * 处理关键字输入，转为安全的分隔标识符的关键字.
     *
     * @param string $string
     *
     * @return string
     */
    public function parseKeyword(string $string): string
    {
        $matches = $this->parseKeywordText($string);

        return $this->parseKeywordToText($matches['keywords'], $matches['alias'], $matches['jsonKeywords']);
    }
}
