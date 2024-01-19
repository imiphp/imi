<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Db\PDO;

use Imi\Pgsql\Test\TPDOPgTest;
use Imi\Pgsql\Test\Unit\Db\DbBaseTestCase;

/**
 * @testdox PDO
 */
class DbTest extends DbBaseTestCase
{
    use TPDOPgTest;

    /**
     * 连接池名.
     */
    protected ?string $poolName = 'maindb';
}
