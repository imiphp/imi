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
}
