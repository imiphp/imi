<?php

declare(strict_types=1);

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
     * @testdox convertArgsToKV
     */
    public function testConvertArgsToKV(): void
    {
        $class = \Imi\Test\Component\Util\ClassObject\Test::class;
        $method = 'imi';
        $args = [1, 2];
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
    }

    /**
     * @testdox getSubClasses
     */
    public function testGetSubClasses(): void
    {
        $this->assertEquals([\Imi\Test\Component\Model\Article::class], ClassObject::getSubClasses(\Imi\Test\Component\Model\Base\ArticleBase::class));
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
}
