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
     *
     * @return void
     */
    public function testBefore()
    {
        $test = App::getBean('TestBeforeClass');
        $result = $test->test(1);
        $this->assertEquals(2, $result);
    }

    /**
     * Aop After.
     *
     * @return void
     */
    public function testAfter()
    {
        $test = App::getBean('TestAfterClass');
        $result = $test->test(1);
        $this->assertEquals(1, $result);
    }

    /**
     * Aop Around.
     *
     * @return void
     */
    public function testAround()
    {
        $test = App::getBean('TestAroundClass');
        $result = $test->test(1);
        $this->assertEquals(4, $result);
    }

    /**
     * Aop AfterReturning.
     *
     * @return void
     */
    public function testAfterReturning()
    {
        $test = App::getBean('TestAfterReturningClass');
        $result = $test->test(1);
        $this->assertEquals(2, $result);
    }

    /**
     * Aop AfterReturning.
     *
     * @return void
     */
    public function testAfterThrowing()
    {
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
