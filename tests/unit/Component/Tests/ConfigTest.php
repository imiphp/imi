<?php

namespace Imi\Test\Component\Tests;

use Imi\Config;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

/**
 * @testdox Config
 */
class ConfigTest extends BaseTest
{
    public function testGet()
    {
        Assert::assertEquals('very six', Config::get('@app.imi-framework'));
        Assert::assertEquals('666', Config::get('@app.yurun')); // .env
        Assert::assertEquals('777', Config::get('@app.yurun2')); // .env
        Assert::assertEquals('default', Config::get('@app.none', 'default'));
    }

    public function testSet()
    {
        $time = time();
        Config::set('@app.test.time', $time);
        Assert::assertEquals($time, Config::get('@app.test.time'));
    }

    public function testHas()
    {
        Assert::assertTrue(Config::has('@app.imi-framework'));
    }

    public function testAddConfig()
    {
        Config::addConfig('test', [
            'a' => [
                'b' => 123,
            ],
            // 配置文件
            'configs'	 => [
                'test'		 => \dirname(__DIR__) . '/config/test.php',
            ],
        ]);
        Assert::assertEquals(123, Config::get('test.a.b'));
        Assert::assertEquals('yurun', Config::get('test.test.imi'));

        Config::removeConfig('test');
        Assert::assertNull(Config::get('test'));
    }

    public function testSetConfig()
    {
        Config::setConfig('test', [
            'a' => [
                'b' => 123,
            ],
        ]);
        Assert::assertEquals(123, Config::get('test.a.b'));

        Config::removeConfig('test');
        Assert::assertNull(Config::get('test'));
    }
}
