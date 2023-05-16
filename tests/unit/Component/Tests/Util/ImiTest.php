<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\App;
use Imi\Test\BaseTest;
use Imi\Test\Component\Util\Imi\TestPropertyClass;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\Process\ProcessAppContexts;

/**
 * @testdox Imi\Util\Imi
 */
class ImiTest extends BaseTest
{
    /**
     * @testdox parseRule
     */
    public function testParseRule(): void
    {
        $this->assertEquals('a\\\\b\:\:c\/d.*e', Imi::parseRule('a\b::c/d*e'));
    }

    /**
     * @testdox checkRuleMatch
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
     */
    public function testCheckClassRule(): void
    {
        $rule = 'a\b::d*e';
        $this->assertTrue(Imi::checkClassRule($rule, 'a\b'));
        $this->assertFalse(Imi::checkClassRule($rule, 'b'));
    }

    /**
     * @testdox checkCompareRules
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
        $this->assertTrue(Imi::checkCompareRules($rules, static function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 0,
                'c' => 2,
                'd' => 1,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, static function (string $name) {
            static $data = [
                'a' => 0,
                'b' => 0,
                'c' => 2,
                'd' => 1,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, static function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 1,
                'c' => 2,
                'd' => 1,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, static function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 0,
                'c' => 1,
                'd' => 1,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, static function (string $name) {
            static $data = [
                'a' => 1,
                'b' => 0,
                'c' => 2,
                'd' => 0,
                'e' => 1,
            ];

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRules($rules, static function (string $name) {
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
     */
    public function testCheckCompareRule(): void
    {
        $this->assertTrue(Imi::checkCompareRule('a=1', function (string $name) {
            static $data = [
                'a' => 1,
            ];
            $this->assertEquals('a', $name);

            return $data[$name];
        }));
        $this->assertFalse(Imi::checkCompareRule('a=2', function (string $name) {
            static $data = [
                'a' => 1,
            ];
            $this->assertEquals('a', $name);

            return $data[$name];
        }));
        $this->assertTrue(Imi::checkCompareRule('!id', function (string $name) {
            $this->assertEquals('id', $name);

            return null;
        }));
        $this->assertTrue(Imi::checkCompareRule('test', function (string $name) {
            $this->assertEquals('test', $name);

            return true;
        }));
    }

    /**
     * @testdox checkCompareValues
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
     */
    public function testGetClassNamespace(): void
    {
        $this->assertEquals('', Imi::getClassNamespace('Redis'));
        $this->assertEquals('', Imi::getClassNamespace('\Redis'));
        $this->assertEquals('Imi\Test\Component\Tests\Util', Imi::getClassNamespace(__CLASS__));
    }

    /**
     * @testdox getClassShortName
     */
    public function testGetClassShortName(): void
    {
        $this->assertEquals('Redis', Imi::getClassShortName('Redis'));
        $this->assertEquals('Redis', Imi::getClassShortName('\Redis'));
        $this->assertEquals('ImiTest', Imi::getClassShortName(__CLASS__));
    }

    /**
     * @testdox getNamespacePath
     */
    public function testGetNamespacePath(): void
    {
        $this->assertEquals(__DIR__, Imi::getNamespacePath('Imi\Test\Component\Tests\Util'));
        $this->assertEquals(__DIR__, Imi::getNamespacePath('Imi\Test\Component\Tests\Util', true));
        $this->assertNull(Imi::getNamespacePath('Imi\Test\Unused\Test'));
    }

    /**
     * @testdox getNamespacePaths
     */
    public function testGetNamespacePaths(): void
    {
        $this->assertEquals([
            __DIR__,
            File::path(\dirname(__DIR__, 5), 'src', 'Test', 'Component', 'Tests', 'Util'),
        ], Imi::getNamespacePaths('Imi\Test\Component\Tests\Util'));
    }

    /**
     * @testdox getClassPropertyValue
     */
    public function testGetClassPropertyValue(): void
    {
        $this->assertEquals(1, Imi::getClassPropertyValue('TestPropertyClass', 'a'));
        $this->assertEquals('bbb', Imi::getClassPropertyValue('TestPropertyClass', 'b'));
        $this->assertEquals(1, Imi::getClassPropertyValue(TestPropertyClass::class, 'a'));
        $this->assertEquals('bbb', Imi::getClassPropertyValue(TestPropertyClass::class, 'b'));
    }

    public function testGetImiCmd(): void
    {
        $cmd = '"' . \PHP_BINARY . '" ' . escapeshellarg(App::get(ProcessAppContexts::SCRIPT_NAME) ?? realpath($_SERVER['SCRIPT_FILENAME'])) . ' ' . escapeshellarg('test');
        $namespace = ' --app-namespace ' . escapeshellarg(App::getNamespace());
        $this->assertEquals($cmd . $namespace, Imi::getImiCmd('test'));

        $this->assertEquals($cmd . ' \'arguments\' -a -b \'bbb\' --cc --dd \'ddd\'' . $namespace, Imi::getImiCmd('test', ['arguments'], ['a', 'b' => 'bbb', 'cc', 'dd' => 'ddd']));
    }

    public function testGetImiCmdArray(): void
    {
        $cmdTpl = [
            \PHP_BINARY,
            App::get(ProcessAppContexts::SCRIPT_NAME) ?? realpath($_SERVER['SCRIPT_FILENAME']),
            'test',
        ];
        $cmd = $cmdTpl;
        $cmd[] = '--app-namespace';
        $cmd[] = App::getNamespace();
        $this->assertEquals($cmd, Imi::getImiCmdArray('test'));

        $cmd = $cmdTpl;
        $cmd[] = 'arguments';
        $cmd[] = '-a';
        $cmd[] = '-b';
        $cmd[] = 'bbb';
        $cmd[] = '--cc';
        $cmd[] = '--dd';
        $cmd[] = 'ddd';
        $cmd[] = '--app-namespace';
        $cmd[] = App::getNamespace();
        $this->assertEquals($cmd, Imi::getImiCmdArray('test', ['arguments'], ['a', 'b' => 'bbb', 'cc', 'dd' => 'ddd']));
    }

    public function testGetModeRuntimePath(): void
    {
        $this->assertEquals(\dirname(__DIR__, 2) . \DIRECTORY_SEPARATOR . '.runtime' . \DIRECTORY_SEPARATOR . 'test' . \DIRECTORY_SEPARATOR . 'a', Imi::getModeRuntimePath('test', 'a'));
    }

    public function testGetCurrentModeRuntimePath(): void
    {
        $this->assertEquals(\dirname(__DIR__, 2) . \DIRECTORY_SEPARATOR . '.runtime' . \DIRECTORY_SEPARATOR . 'cli' . \DIRECTORY_SEPARATOR . 'a', Imi::getCurrentModeRuntimePath('a'));
    }

    public function testEval(): void
    {
        $this->assertEquals(3, Imi::eval('return 1+2;'));
        $this->assertEquals(4, Imi::eval('return 2+2;', null, false));
    }

    public function testCheckAppType(): void
    {
        $this->assertTrue(Imi::checkAppType('cli'));
        $this->assertFalse(Imi::checkAppType('swoole'));
    }

    public function testFormatByte(): void
    {
        $this->assertEquals('1.00 B', Imi::formatByte(1));
        $this->assertEquals('1.00 KB', Imi::formatByte(1024));
        $this->assertEquals('1.00 MB', Imi::formatByte(1024 * 1024));
        $this->assertEquals('1.00 GB', Imi::formatByte(1024 * 1024 * 1024));
        $this->assertEquals('1.00 TB', Imi::formatByte(1024 * 1024 * 1024 * 1024));
        $this->assertEquals('1.00 PB', Imi::formatByte(1024 * 1024 * 1024 * 1024 * 1024));
        $this->assertEquals('1024.00 PB', Imi::formatByte(1024 * 1024 * 1024 * 1024 * 1024 * 1024));

        $this->assertEquals('1.006 KB', Imi::formatByte(1030, 3));
        $this->assertEquals('1.006', Imi::formatByte(1030, 3, false));
    }
}
