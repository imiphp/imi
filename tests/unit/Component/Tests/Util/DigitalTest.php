<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\Digital;

/**
 * @testdox Imi\Util\Digital
 */
class DigitalTest extends BaseTest
{
    /**
     * @testdox scientificToNum
     */
    public function testScientificToNum(): void
    {
        $this->assertEquals(0.000021, Digital::scientificToNum('2.1E-5', 6));
    }
}
