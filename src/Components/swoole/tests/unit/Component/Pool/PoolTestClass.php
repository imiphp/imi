<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests\Pool;

use Imi\Pool\Annotation\PoolResource;
use Imi\Swoole\Db\Pool\CoroutineDbPool;

class PoolTestClass
{
    /**
     * @PoolResource("maindb")
     */
    public CoroutineDbPool $maindbPool;
}
