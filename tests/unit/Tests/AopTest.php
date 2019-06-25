<?php
namespace Imi\Test\Tests;

use Imi\Test\BaseTest;
use Imi\App;

class AopTest extends BaseTest
{
    /**
     * Aop Before
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
     * Aop After
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
     * Aop Around
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
     * Aop AfterReturning
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
     * Aop AfterReturning
     *
     * @return void
     */
    public function testAfterThrowing()
    {
        $test = App::getBean('TestAfterThrowingClass');

        $throwable = null;
        try {
            $test->testCancelThrow();
        } catch(\Throwable $th) {
            $throwable = $th;
        }
        $this->assertEquals(null, $throwable);

        $throwable = null;
        try {
            $test->testNotCancelThrow();
        } catch(\Throwable $th) {
            $throwable = $th;
        }
        $this->assertNotEquals(null, $throwable);
        $this->assertEquals('test', $th->getMessage());
    }
}
