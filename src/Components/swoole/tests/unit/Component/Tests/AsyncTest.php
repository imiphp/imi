<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\App;
use Imi\Async\Exception\AsyncTimeoutException;
use Imi\Swoole\Test\BaseTest;
use Imi\Swoole\Test\Component\Async\AsyncTester;

use function Yurun\Swoole\Coroutine\goWait;

class AsyncTest extends BaseTest
{
    public function testAsync(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $time = microtime(true);
        $asyncTester->testAsync1();
        $this->assertLessThanOrEqual(1, microtime(true) - $time);

        $this->assertEquals(3, $asyncTester->testAsync2(1, 2)->get());
        $this->assertEquals(3, $asyncTester->testAsync3(1, 2)->get());
    }

    public function testDefer(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $time = microtime(true);
        $results = goWait(static function () use ($asyncTester) {
            return [
                // @phpstan-ignore-next-line
                $asyncTester->testDefer1(),
                $asyncTester->testDefer2(1, 2),
                $asyncTester->testDefer3(1, 2),
            ];
        });
        $this->assertLessThanOrEqual(1, microtime(true) - $time);

        $this->assertNull($results[0]);
        $this->assertEquals(3, $results[1]->get());
        $this->assertEquals(3, $results[2]->get());
    }

    public function testDeferAsync(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $time = microtime(true);
        $results = goWait(static function () use ($asyncTester) {
            return [
                // @phpstan-ignore-next-line
                $asyncTester->testDeferAsync1(),
                $asyncTester->testDeferAsync2(1, 2),
                $asyncTester->testDeferAsync3(1, 2),
            ];
        });
        $this->assertLessThanOrEqual(1, microtime(true) - $time);

        $this->assertNull($results[0]);
        $this->assertEquals(3, $results[1]->get());
        $this->assertEquals(3, $results[2]->get());
    }

    public function testTimeout(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $this->expectException(AsyncTimeoutException::class);
        $asyncTester->testAsync4()->get(0.001);
    }

    public function testException(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $this->expectException(\RuntimeException::class);
        $asyncTester->testException()->get();
    }

    public function testExceptionNotGet(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $asyncTester->testException();
        $this->assertTrue(true);
    }
}
