<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\FullText;

use Imi\Util\Traits\TStaticClass;

/**
 * PostgreSQL tsquery 函数.
 */
class TsQuery
{
    use TStaticClass;

    public const TO_TSQUERY = 'to_tsquery';

    public const PLAINTO_TSQUERY = 'plainto_tsquery';

    public const PHRASETO_TSQUERY = 'phraseto_tsquery';

    public const WEBSEARCH_TO_TSQUERY = 'websearch_to_tsquery';
}
