<?php

declare(strict_types=1);

namespace Imi\Db\Annotation;

class RollbackType
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 回滚所有（rollback）.
     */
    public const ALL = 'all';

    /**
     * 回滚部分（rollback to xxx）.
     */
    public const PART = 'part';
}
