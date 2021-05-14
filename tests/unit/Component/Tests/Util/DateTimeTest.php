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
        for ($_ = 0; $_ < 3; ++$_)
        {
            try
            {
                $th = null;
                $di = new \DateInterval('PT1S');
                $sec = DateTime::getSecondsByInterval($di);
                $this->assertTrue($sec <= 1);
                break;
            }
            catch (\Throwable $th)
            {
                sleep(1);
            }
        }
        if (isset($th))
        {
            throw $th;
        }
    }
}
