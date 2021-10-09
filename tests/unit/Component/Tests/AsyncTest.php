<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Test\BaseTest;
use Imi\Test\Component\Async\AsyncTester;
use Imi\Test\Component\Async\AsyncTesterPHP8;

class AsyncTest extends BaseTest
{
    public function test()
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $this->assertEquals(3, $asyncTester->test1(1, 2)->get());
        $this->assertEquals(3, $asyncTester->test2(1, 2)->get());

        if (\PHP_VERSION_ID >= 80000)
        {
            /** @var AsyncTesterPHP8 $asyncTesterPHP8 */
            $asyncTesterPHP8 = App::getBean('AsyncTesterPHP8');
            $this->assertEquals(3, $asyncTesterPHP8->test1(1, 2)->get());
        }
    }
}
