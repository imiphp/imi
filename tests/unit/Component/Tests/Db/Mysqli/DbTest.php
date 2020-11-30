<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db\Mysqli;

use Imi\Test\Component\Tests\Db\DbBaseTest;

/**
 * @testdox Mysqli MySQL
 */
class DbTest extends DbBaseTest
{
    /**
     * 连接池名.
     *
     * @var string
     */
    protected $poolName = 'mysqli';
}
