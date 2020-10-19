<?php

namespace Imi\Db\Query\Lock;

class MysqlLock
{
    /**
     * 排它锁
     */
    const FOR_UPDATE = 1;

    /**
     * 共享锁
     */
    const SHARED = 2;
}
