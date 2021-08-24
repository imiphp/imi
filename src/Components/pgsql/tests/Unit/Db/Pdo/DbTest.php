<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Db\Pdo;

use Imi\Pgsql\Test\Unit\Db\DbBaseTest;

/**
 * @testdox PDO
 */
class DbTest extends DbBaseTest
{
    /**
     * 连接池名.
     *
     * @var string
     */
    protected $poolName = 'maindb';
}
