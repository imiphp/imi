<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\Bit;

/**
 * @testdox Imi\Util\Bit
 */
class BitTest extends BaseTest
{
    /**
     * @testdox has
     */
    public function testHas(): void
    {
        $value = 1 + 4 + 256;
        $this->assertTrue(Bit::has($value, 1));
        $this->assertTrue(Bit::has($value, 4));
        $this->assertTrue(Bit::has($value, 256));
        $this->assertTrue(Bit::has($value, 5));
        $this->assertFalse(Bit::has($value, 8));
        $this->assertFalse(Bit::has($value, 16));
    }
}
