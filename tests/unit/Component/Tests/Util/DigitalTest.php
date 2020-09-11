<?php

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
     *
     * @return void
     */
    public function testScientificToNum()
    {
        $this->assertEquals(0.000021, Digital::scientificToNum(2.1E-5, 6));
    }
}
