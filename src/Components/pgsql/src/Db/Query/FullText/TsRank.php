<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\FullText;

/**
 * PostgreSQL ts_rank 函数.
 */
use Imi\Util\Traits\TStaticClass;

class TsRank
{
    use TStaticClass;

    public const TS_RANK = 'ts_rank';

    public const TS_RANK_CD = 'ts_rank_cd';
}
