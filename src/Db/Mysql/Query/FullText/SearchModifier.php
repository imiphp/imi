<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Query\FullText;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * 搜索修饰符.
 */
class SearchModifier extends BaseEnum
{
    use \Imi\Util\Traits\TStaticClass;

    #[EnumItem(text: '自然语言模式')]
    public const IN_NATURAL_LANGUAGE_MODE = 'IN NATURAL LANGUAGE MODE';

    #[EnumItem(text: '自然语言模式，带查询扩展')]
    public const IN_NATURAL_LANGUAGE_MODE_WITH_QUERY_EXPANSION = 'IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION';

    #[EnumItem(text: '布尔模式')]
    public const IN_BOOLEAN_MODE = 'IN BOOLEAN MODE';

    #[EnumItem(text: '带查询扩展')]
    public const WITH_QUERY_EXPANSION = 'WITH QUERY EXPANSION';
}
