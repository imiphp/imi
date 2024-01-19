<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Query\FullText;

/**
 * 搜索修饰符.
 */
class SearchModifier
{
    /**
     * 自然语言模式.
     */
    public const IN_NATURAL_LANGUAGE_MODE = 'IN NATURAL LANGUAGE MODE';

    /**
     * 自然语言模式，带查询扩展.
     */
    public const IN_NATURAL_LANGUAGE_MODE_WITH_QUERY_EXPANSION = 'IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION';

    /**
     * 布尔模式.
     */
    public const IN_BOOLEAN_MODE = 'IN BOOLEAN MODE';

    /**
     * 带查询扩展.
     */
    public const WITH_QUERY_EXPANSION = 'WITH QUERY EXPANSION';
}
