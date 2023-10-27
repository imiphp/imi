<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test\Unit\Db\Swoole;

use Imi\Pgsql\Test\TSwoolePgTest;
use Imi\Pgsql\Test\Unit\Db\DbBaseTestCase;

/**
 * @testdox Swoole
 */
class DbTest extends DbBaseTestCase
{
    use TSwoolePgTest;

    /**
     * 连接池名.
     */
    protected ?string $poolName = 'swoole';
}
