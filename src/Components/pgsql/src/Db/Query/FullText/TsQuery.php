<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\FullText;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * PostgreSQL tsquery 函数.
 */
class TsQuery extends BaseEnum
{
    #[EnumItem(text: 'to_tsquery')]
    public const TO_TSQUERY = 'to_tsquery';

    #[EnumItem(text: 'plainto_tsquery')]
    public const PLAINTO_TSQUERY = 'plainto_tsquery';

    #[EnumItem(text: 'phraseto_tsquery')]
    public const PHRASETO_TSQUERY = 'phraseto_tsquery';

    #[EnumItem(text: 'websearch_to_tsquery')]
    public const WEBSEARCH_TO_TSQUERY = 'websearch_to_tsquery';
}
