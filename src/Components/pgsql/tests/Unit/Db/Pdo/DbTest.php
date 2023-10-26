<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Db\Pdo;

use Imi\Pgsql\Test\TPdoPgTest;
use Imi\Pgsql\Test\Unit\Db\DbBaseTestCase;

/**
 * @testdox PDO
 */
class DbTest extends DbBaseTestCase
{
    use TPdoPgTest;

    /**
     * 连接池名.
     */
    protected ?string $poolName = 'maindb';
}
