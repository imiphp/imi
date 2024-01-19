<?php

declare(strict_types=1);

namespace Imi\Db\Test\Tests\Mysqli;

use Imi\Db\Test\Tests\DbBaseTestCase;

/**
 * @testdox Mysqli MySQL
 */
class DbTest extends DbBaseTestCase
{
    /**
     * 连接池名.
     */
    protected ?string $poolName = 'mysqli';
}
