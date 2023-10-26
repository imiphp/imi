<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Performance;

use Imi\Test\Component\Performance\BaseDbTestCase;

/**
 * @testdox Performance:SwooleMysql
 */
class SwooleMysqlDbTest extends BaseDbTestCase
{
    public function getPoolName(): string
    {
        return 'swooleMysql';
    }
}
