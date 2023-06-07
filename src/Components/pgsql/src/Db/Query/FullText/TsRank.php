<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Query\FullText;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * PostgreSQL ts_rank 函数.
 */
class TsRank extends BaseEnum
{
    /**
     * @EnumItem(text="ts_rank")
     */
    public const TS_RANK = 'ts_rank';

    /**
     * @EnumItem(text="ts_rank_cd")
     */
    public const TS_RANK_CD = 'ts_rank_cd';
}
