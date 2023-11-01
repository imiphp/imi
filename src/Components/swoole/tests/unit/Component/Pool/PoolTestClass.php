<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Pool;

use Imi\Db\Pool\DbResource;
use Imi\Pool\Annotation\PoolResource;

class PoolTestClass
{
    #[PoolResource(name: 'maindb')]
    public DbResource $db;
}
