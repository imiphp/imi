<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Test\BaseTest;
use Test\TestContext;

/**
 * @testdox App
 */
class AppTest extends BaseTest
{
    public function testSetAndGet(): void
    {
        App::set('a', '111');
        $this->assertEquals('111', App::get('a'));
        $this->assertEquals('222', App::get('b', '222'));
    }

    public function testReadonly(): void
    {
        App::set('test', 'abc', true);
        $this->assertEquals('abc', App::get('test'));
        App::set('test', 'def');
        $this->assertEquals('def', App::get('test'));
        require_once \dirname(__DIR__) . '/App/TestContext.php';
        $this->expectException(\RuntimeException::class);
        TestContext::set();
    }

    public function testQuickStart(): void
    {
        $content = $this->php(\dirname(__DIR__) . '/test.php');
        $this->assertTrue(str_contains($content, 'Test quick start'), 'content: ' . $content);
    }
}
