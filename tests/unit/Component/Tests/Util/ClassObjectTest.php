<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Bean\ReflectionContainer;
use Imi\Test\BaseTest;
use Imi\Util\ClassObject;

/**
 * @testdox Imi\Util\ClassObject
 */
class ClassObjectTest extends BaseTest
{
    /**
     * @testdox isAnymous
     */
    public function testIsAnymous(): void
    {
        $obj = new class() {
        };
        $this->assertTrue(ClassObject::isAnymous($obj));

        $obj = new \stdClass();
        $this->assertFalse(ClassObject::isAnymous($obj));
    }

    /**
     * @testdox parseSameLevelClassName
     */
    public function testParseSameLevelClassName(): void
    {
        $className = __CLASS__;
        $this->assertEquals($className, ClassObject::parseSameLevelClassName($className, 'Test1\A'));

        $this->assertEquals('Test1\Test3\A', ClassObject::parseSameLevelClassName('Test3\A', 'Test1\A'));
    }

    /**
     * @testdox convertArrayToKV and convertKVToArray
     */
    public function testConvertArgsToKV(): void
    {
        $class = \Imi\Test\Component\Util\ClassObject\Test::class;
        $method = 'imi';
        $args = [1, 2];
        $params = ReflectionContainer::getMethodReflection($class, $method)->getParameters();

        // convertArgsToKV
        // $keepNotExistArgs = true
        $this->assertEquals([
            'a' => 1,
            'b' => 2,
            'c' => 'imi.com',
        ], ClassObject::convertArgsToKV($class, $method, $args));
        // $keepNotExistArgs = false
        $this->assertEquals([
            'a' => 1,
            'b' => 2,
        ], ClassObject::convertArgsToKV($class, $method, $args, false));

        // $keepNotExistArgs = true
        $kv = ClassObject::convertArrayToKV($params, $args);
        $this->assertEquals([
            'a' => 1,
            'b' => 2,
            'c' => 'imi.com',
        ], $kv);
        $this->assertEquals([
            1,
            2,
            'imi.com',
        ], ClassObject::convertKVToArray($params, $kv));

        // $keepNotExistArgs = false
        $kv = ClassObject::convertArrayToKV($params, $args, false);
        $this->assertEquals([
            'a' => 1,
            'b' => 2,
        ], $kv);
        $this->assertEquals([
            1,
            2,
            'imi.com',
        ], ClassObject::convertKVToArray($params, $kv));
    }

    /**
     * @testdox getSubClasses
     */
    public function testGetSubClasses(): void
    {
        $this->assertEquals([\Imi\Test\Component\Model\Unused::class], ClassObject::getSubClasses(\Imi\Test\Component\Model\Base\UnusedBase::class));
        $this->assertEquals([], ClassObject::getSubClasses(\Imi\Test\Component\Model\Base\ArticleBase::class, [
            \Imi\Test\Component\Model\Member::class,
        ]));
    }

    /**
     * @testdox inNamespace
     */
    public function testInNamespace(): void
    {
        $this->assertTrue(ClassObject::inNamespace('Imi\Test\Component\Tests\Util', __CLASS__));
        $this->assertTrue(ClassObject::inNamespace('', \Redis::class));
    }

    /**
     * @testdox newInstance
     */
    public function testNewInstance(): void
    {
        $class = \Imi\Test\Component\Util\ClassObject\Test::class;
        $object = ClassObject::newInstance($class, [
            'a' => 111,
            'b' => 222,
            'c' => '333',
        ]);
        $this->assertInstanceOf($class, $object);
        $this->assertEquals(111, $object->a);
        $this->assertEquals(222, $object->b);
        $this->assertEquals('333', $object->c);
    }
}
