<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Db\Swoole;

use Imi\Pgsql\Test\Unit\Db\DbBaseTest;

/**
 * @testdox Swoole
 */
class DbTest extends DbBaseTest
{
    /**
     * 连接池名.
     *
     * @var string
     */
    protected $poolName = 'swoole';
}
