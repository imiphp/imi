<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Test\BaseTest;

/**
 * @testdox Aop
 */
class AopTest extends BaseTest
{
    /**
     * Aop Before.
     */
    public function testBefore(): void
    {
        /** @var \Imi\Test\Component\Aop\Classes\TestBeforeClass $test */
        $test = App::getBean('TestBeforeClass');
        $result = $test->test(1);
        $this->assertEquals(2, $result);
    }

    /**
     * Aop After.
     */
    public function testAfter(): void
    {
        /** @var \Imi\Test\Component\Aop\Classes\TestAfterClass $test */
        $test = App::getBean('TestAfterClass');
        $result = $test->test(1);
        $this->assertEquals(1, $result);
    }

    /**
     * Aop Around.
     */
    public function testAround(): void
    {
        /** @var \Imi\Test\Component\Aop\Classes\TestAroundClass $test */
        $test = App::getBean('TestAroundClass');
        $result = $test->test(1);
        $this->assertEquals(4, $result);
    }

    /**
     * Aop AfterReturning.
     */
    public function testAfterReturning(): void
    {
        /** @var \Imi\Test\Component\Aop\Classes\TestAfterReturningClass $test */
        $test = App::getBean('TestAfterReturningClass');
        $result = $test->test(1);
        $this->assertEquals(2, $result);
    }

    /**
     * Aop AfterReturning.
     */
    public function testAfterThrowing(): void
    {
        /** @var \Imi\Test\Component\Aop\Classes\TestAfterThrowingClass $test */
        $test = App::getBean('TestAfterThrowingClass');

        $throwable = null;
        try
        {
            $test->testCancelThrow();
        }
        catch (\Throwable $th)
        {
            $throwable = $th;
        }
        $this->assertNull($throwable);

        $throwable = null;
        try
        {
            $test->testNotCancelThrow();
        }
        catch (\Throwable $th)
        {
            $throwable = $th;
        }
        $this->assertNotNull($throwable);
        $this->assertEquals('test', $throwable->getMessage());
    }
}
