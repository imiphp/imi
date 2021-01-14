<?php

declare(strict_types=1);

namespace Imi\Test\Component\Performance;

/**
 * @testdox Performance:mysqli
 */
class MysqliDbTest extends BaseDbTest
{
    public function getPoolName(): string
    {
        return 'mysqli';
    }
}
