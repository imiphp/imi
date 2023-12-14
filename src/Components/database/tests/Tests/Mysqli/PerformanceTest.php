<?php

declare(strict_types=1);

namespace Imi\Db\Test\Tests\Mysqli;

use Imi\Db\Test\Tests\BasePerformanceTestCase;

/**
 * @testdox Performance:PDO
 */
class PerformanceTest extends BasePerformanceTestCase
{
    public function getPoolName(): string
    {
        return 'mysqli';
    }
}
