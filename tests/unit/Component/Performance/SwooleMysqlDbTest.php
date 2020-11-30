<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Performance;

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
