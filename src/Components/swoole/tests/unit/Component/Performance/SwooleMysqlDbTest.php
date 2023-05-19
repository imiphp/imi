<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Performance;

use Imi\Test\Component\Performance\BaseDbTest;

/**
 * @testdox Performance:SwooleMysql
 */
class SwooleMysqlDbTest extends BaseDbTest
{
    public function getPoolName(): string
    {
        return 'swooleMysql';
    }
}
