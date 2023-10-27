<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Db\Mysqli;

use Imi\Test\Component\Tests\Db\DbBaseTestCase;

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
