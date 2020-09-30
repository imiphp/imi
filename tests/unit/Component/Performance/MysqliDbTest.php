<?php
namespace Imi\Test\Component\Tests\Performance;

/**
 * @testdox Performance:mysqli
 */
class Mysqli extends BaseDbTest
{
    public function getPoolName(): string
    {
        return 'mysqli';
    }
}
