<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests\Db\Swoole;

use Imi\Test\Component\Tests\Db\DbBaseTestCase;

/**
 * @testdox Swoole MySQL
 */
class DbTest extends DbBaseTestCase
{
    /**
     * 连接池名.
     */
    protected ?string $poolName = 'swooleMysql';
}
