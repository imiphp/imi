<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Bean\ReflectionContainer;
use Imi\Test\BaseTest;
use Imi\Test\Component\Util\ChildClass;
use Imi\Test\Component\Util\ParentClass;
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
        ], ClassObject::convertArgsToKV($class, '__construct', $args));
        // $keepNotExistArgs = false
        $this->assertEquals([
            'a' => 1,
            'b' => 2,
        ], ClassObject::convertArgsToKV($class, '__construct', $args, false));

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
        $this->assertEquals([], ClassObject::convertKVToArray([], []));
        $this->assertEquals([
            1,
            2,
            'imi.com',
        ], ClassObject::convertKVToArray($params, $kv));
        try
        {
            ClassObject::convertKVToArray($params, []);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertStringMatchesFormat('Argument %s of %s::__construct() does not found', $e->getMessage());
        }

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

        $params = ReflectionContainer::getMethodReflection($class, 'noParam')->getParameters();
        $kv = ClassObject::convertArrayToKV($params, []);
        $this->assertEquals([], $kv);

        $params = ReflectionContainer::getMethodReflection($class, 'variadic')->getParameters();
        $kv = ClassObject::convertArrayToKV($params, ['a', 'b']);
        $this->assertEquals(['params' => [
            'a',
            'b',
        ]], $kv);
    }

    /**
     * @testdox getSubClasses
     */
    public function testGetSubClasses(): void
    {
        $this->assertEquals([ChildClass::class], ClassObject::getSubClasses(ParentClass::class));
        $this->assertEquals([], ClassObject::getSubClasses(ParentClass::class, [
            __CLASS__,
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

        $class = \Imi\Test\Component\Util\ClassObject\TestNoConstrauct::class;
        $object = ClassObject::newInstance($class, []);
        $this->assertInstanceOf($class, $object);
    }
}
