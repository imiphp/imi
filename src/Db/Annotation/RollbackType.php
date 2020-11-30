<?php

declare(strict_types=1);

namespace Imi\Db\Annotation;

class RollbackType
{
    /**
     * 回滚所有（rollback）.
     */
    const ALL = 'all';

    /**
     * 回滚部分（rollback to xxx）.
     */
    const PART = 'part';

    private function __construct()
    {
    }
}
