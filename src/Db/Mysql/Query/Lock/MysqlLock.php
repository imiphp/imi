<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Query\Lock;

class MysqlLock
{
    /**
     * 排它锁
     */
    public const FOR_UPDATE = 1;

    /**
     * 共享锁
     */
    public const SHARED = 2;
}
