<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Test\Component\Util\Imi\TestPropertyClass;
use Imi\Util\Imi;

/**
 * @testdox Imi\Util\Imi
 */
class ImiTest extends BaseTest
{
    /**
     * @testdox parseRule
     *
     * @return void
     */
    public function testParseRule(): void
    {
        $this->assertEquals('a\\\\b\:\:c\/d.*e', Imi::parseRule('a\b::c/d*e'));
    }

    /**
     * @testdox checkRuleMatch
     *
     * @return void
     */
    public function testCheckRuleMatch(): void
    {
        $rule = 'a\b::c/d*e';
        $this->assertTrue(Imi::checkRuleMatch($rule, 'a\b::c/d123e'));
        $this->assertFalse(Imi::checkRuleMatch($rule, 'b::c/d123e'));
        $this->assertFalse(Imi::checkRuleMatch($rule, 'a\b::c/d123ef'));
    }

    /**
     * @testdox checkClassMethodRule
     *
     * @return void
     */
    public function testCheckClassMethodRule(): void
    {
        $rule = 'a\b::d*e';
        $this->assertTrue(Imi::checkClassMethodRule($rule, 'a\b', 'd123e'));
        $this->assertFalse(Imi::checkClassMethodRule($rule, 'b', 'd123e'));
        $this->assertFalse(Imi::checkClassMethodRule($rule, 'a\b', 'd123ef'));
    }

    /**
     * @testdox checkClassRule
     *
     * @return void
     */
    public function testCheckClassRule(): void
    {
        $rule = 'a\b::d*e';
        $this->assertTrue(Imi::checkClassRule($rule, 'a\b'));
        $this->assertFalse(Imi::checkClassRule($rule, 'b'));
    }

    /**
     * @testdox checkCompareRules
     *
     * @return void
     */
    public function testCheckCompareRules(): void
    {
        $rules = [
            'a' => '1',
            'b' => '[^1]',
            'c=2',
            'd!=0',
            'e<>0',
        ];
        $this->assertTrue(Imi::checkCompareRules($rules, function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 0,
                'c' => 2,
                'd' => 1,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, function (string $name) {
            static $data = [
                'a' => 0,
                'b' => 0,
                'c' => 2,
                'd' => 1,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 1,
                'c' => 2,
                'd' => 1,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 0,
                'c' => 1,
                'd' => 1,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 0,
                'c' => 2,
                'd' => 0,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 0,
                'c' => 2,
                'd' => 1,
                'e' => 0,
            ];

            return $data[$name];
        }));
    }

    /**
     * @testdox checkCompareRule
     *
     * @return void
     */
    public function testCheckCompareRule(): void
    {
        $this->assertTrue(Imi::checkCompareRule('a=1', function (string $name) {
            static $data = [
                'a' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRule('a=2', function (string $name) {
            static $data = [
                'a' => 1,
            ];

            return $data[$name];
        }));
    }

    /**
     * @testdox checkCompareValues
     *
     * @return void
     */
    public function testCheckCompareValues(): void
    {
        $rules = [
            '!1',
            '!2',
        ];
        $this->assertTrue(Imi::checkCompareValues($rules, '0'));
        $this->assertFalse(Imi::checkCompareValues($rules, '1'));
        $this->assertFalse(Imi::checkCompareValues($rules, '2'));

        $rules = [
            '!1',
            '!2',
            '3',
        ];
        $this->assertFalse(Imi::checkCompareValues($rules, '0'));
        $this->assertFalse(Imi::checkCompareValues($rules, '1'));
        $this->assertFalse(Imi::checkCompareValues($rules, '2'));
        $this->assertTrue(Imi::checkCompareValues($rules, '3'));
    }

    /**
     * @testdox checkCompareValue
     *
     * @return void
     */
    public function testCheckCompareValue(): void
    {
        $this->assertTrue(Imi::checkCompareValue('123', '123'));
        $this->assertFalse(Imi::checkCompareValue('123', '1234'));
        $this->assertFalse(Imi::checkCompareValue('!123', '123'));
        $this->assertTrue(Imi::checkCompareValue('!123', '1234'));
    }

    /**
     * @testdox parseDotRule
     *
     * @return void
     */
    public function testCheckParseDotRule(): void
    {
        $this->assertEquals([
            'a',
            'b.c',
            'd',
        ], Imi::parseDotRule('a.b\.c.d'));
    }

    /**
     * @testdox getClassNamespace
     *
     * @return void
     */
    public function testGetClassNamespace(): void
    {
        $this->assertEquals('', Imi::getClassNamespace('Redis'));
        $this->assertEquals('', Imi::getClassNamespace('\Redis'));
        $this->assertEquals('Imi\Test\Component\Tests\Util', Imi::getClassNamespace(__CLASS__));
    }

    /**
     * @testdox getClassShortName
     *
     * @return void
     */
    public function testGetClassShortName(): void
    {
        $this->assertEquals('Redis', Imi::getClassShortName('Redis'));
        $this->assertEquals('Redis', Imi::getClassShortName('\Redis'));
        $this->assertEquals('ImiTest', Imi::getClassShortName(__CLASS__));
    }

    /**
     * @testdox getNamespacePath
     *
     * @return void
     */
    public function testGetNamespacePath(): void
    {
        $this->assertEquals(__DIR__, Imi::getNamespacePath('Imi\Test\Component\Tests\Util'));
    }

    /**
     * @testdox getNamespacePaths
     *
     * @return void
     */
    public function testGetNamespacePaths(): void
    {
        $this->assertEquals([
            __DIR__,
            \dirname(__DIR__, 5) . '/src/Test/Component/Tests/Util',
        ], Imi::getNamespacePaths('Imi\Test\Component\Tests\Util'));
    }

    /**
     * @testdox getClassPropertyValue
     *
     * @return void
     */
    public function testGetClassPropertyValue(): void
    {
        $this->assertEquals(1, Imi::getClassPropertyValue('TestPropertyClass', 'a'));
        $this->assertEquals('bbb', Imi::getClassPropertyValue('TestPropertyClass', 'b'));
        $this->assertEquals(1, Imi::getClassPropertyValue(TestPropertyClass::class, 'a'));
        $this->assertEquals('bbb', Imi::getClassPropertyValue(TestPropertyClass::class, 'b'));
    }
}
