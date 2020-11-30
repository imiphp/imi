<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Performance;

/**
 * @testdox Performance:PDO
 */
class PdoDbTest extends BaseDbTest
{
    public function getPoolName(): string
    {
        return 'maindb';
    }
}
