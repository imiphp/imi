<?php

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\DateTime;

/**
 * @testdox Imi\Util\DateTimeTest
 */
class DateTimeTest extends BaseTest
{
    /**
     * @testdox getSecondsByInterval
     *
     * @return void
     */
    public function testGetSecondsByInterval()
    {
        $di = new \DateInterval('PT1S');
        $sec = DateTime::getSecondsByInterval($di);
        $this->assertTrue($sec <= 1);
    }
}
