<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Test\BaseTest;
use Imi\Test\Component\Async\AsyncTester;

class AsyncTest extends BaseTest
{
    public function testAsync(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $this->assertEquals(3, $asyncTester->testAsync1(1, 2)->get());
        $this->assertEquals(3, $asyncTester->testAsync2(1, 2)->get());
    }

    public function testDefer(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $this->assertEquals(3, $asyncTester->testDefer1(1, 2)->get());
        $this->assertEquals(3, $asyncTester->testDefer2(1, 2)->get());
    }

    public function testDeferAsync(): void
    {
        /** @var AsyncTester $asyncTester */
        $asyncTester = App::getBean('AsyncTester');

        $this->assertEquals(3, $asyncTester->testDeferAsync1(1, 2)->get());
        $this->assertEquals(3, $asyncTester->testDeferAsync2(1, 2)->get());
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
