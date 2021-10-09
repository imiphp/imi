<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\App;
use Imi\Async\Exception\AsyncTimeoutException;
use Imi\Swoole\Test\BaseTest;
use Imi\Test\Component\Async\AsyncTester;

class AsyncTest extends BaseTest
{
    public function testTimeout()
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $time = microtime(true);
        $asyncTester->test1();
        $this->assertLessThanOrEqual(1, microtime(true) - $time);

        $this->assertEquals(3, $asyncTester->test2(1, 2)->get());
        $this->assertEquals(3, $asyncTester->test3(1, 2)->get());

        $this->expectException(AsyncTimeoutException::class);
        $asyncTester->test4()->get(0.001);
    }
}
