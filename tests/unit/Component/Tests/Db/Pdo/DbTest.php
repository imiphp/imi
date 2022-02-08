<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db\Pdo;

use Imi\Test\Component\Tests\Db\DbBaseTest;

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
    protected ?string $poolName = 'maindb';
}
