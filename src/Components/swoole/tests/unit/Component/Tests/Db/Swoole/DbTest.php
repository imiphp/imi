<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests\Db\Swoole;

use Imi\Test\Component\Tests\Db\DbBaseTest;

/**
 * @testdox Swoole MySQL
 */
class DbTest extends DbBaseTest
{
    /**
     * 连接池名.
     *
     * @var string
     */
    protected $poolName = 'swooleMysql';
}
