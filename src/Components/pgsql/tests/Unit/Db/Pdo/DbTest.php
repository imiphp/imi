<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Db\Pdo;

use Imi\Pgsql\Test\TPdoPgTest;
use Imi\Pgsql\Test\Unit\Db\DbBaseTest;

/**
 * @testdox PDO
 */
class DbTest extends DbBaseTest
{
    use TPdoPgTest;

    /**
     * 连接池名.
     */
    protected ?string $poolName = 'maindb';
}
