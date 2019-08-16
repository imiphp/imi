<?php
namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\ClassObject;

/**
 * @testdox Imi\Util\ClassObject
 */
class ClassObjectTest extends BaseTest
{
    /**
     * @testdox isAnymous
     *
     * @return void
     */
    public function testIsAnymous()
    {
        $obj = new class{};
        $this->assertTrue(ClassObject::isAnymous($obj));

        $obj = new \stdClass;
        $this->assertFalse(ClassObject::isAnymous($obj));
    }

    /**
     * @testdox parseSameLevelClassName
     *
     * @return void
     */
    public function testParseSameLevelClassName()
    {
        $className = __CLASS__;
        $this->assertEquals($className, ClassObject::parseSameLevelClassName($className, 'Test1\A'));

        $this->assertEquals('Test1\Test3\A', ClassObject::parseSameLevelClassName('Test3\A', 'Test1\A'));
    }

    /**
     * @testdox convertArgsToKV
     *
     * @return void
     */
    public function testConvertArgsToKV()
    {
        $class = \Imi\Test\Component\Util\ClassObject\Test::class;
        $method = 'imi';
        $args = [1, 2];
        // $keepNotExistArgs = true
        $this->assertEquals([
            'a' =>  1,
            'b' =>  2,
            'c' =>  'imi.com',
        ], ClassObject::convertArgsToKV($class, $method, $args));
        // $keepNotExistArgs = false
        $this->assertEquals([
            'a' =>  1,
            'b' =>  2,
        ], ClassObject::convertArgsToKV($class, $method, $args, false));
    }

}
