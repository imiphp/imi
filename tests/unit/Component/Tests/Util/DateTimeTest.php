<?php

declare(strict_types=1);

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
     */
    public function testGetSecondsByInterval(): void
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
