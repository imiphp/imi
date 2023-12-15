<?php

declare(strict_types=1);

namespace Imi\Db\Test\Tests\PDO;

use Imi\Db\Test\Tests\DbBaseTestCase;

/**
 * @testdox PDO
 */
class DbTest extends DbBaseTestCase
{
    /**
     * 连接池名.
     */
    protected ?string $poolName = 'maindb';
}
