<?php

namespace Imi\Db\Annotation;

abstract class RollbackType
{
    /**
     * 回滚所有（rollback）.
     */
    const ALL = 'all';

    /**
     * 回滚部分（rollback to xxx）.
     */
    const PART = 'part';
}
