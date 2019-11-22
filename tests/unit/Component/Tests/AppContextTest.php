<?php
namespace Imi\Test\Component\Tests;

use Imi\App;
use Test\TestContext;
use Imi\Test\BaseTest;

/**
 * @testdox AppContext
 */
class AppContextTest extends BaseTest
{
    public function testSetAndGet()
    {
        App::set('a', '111');
        $this->assertEquals('111', App::get('a'));
        $this->assertEquals('222', App::get('b', '222'));
    }

    public function testReadonly()
    {
        App::set('test', 'abc', true);
        $this->assertEquals('abc', App::get('test'));
        App::set('test', 'def');
        $this->assertEquals('def', App::get('test'));
        require_once dirname(__DIR__) . '/App/TestContext.php';
        $this->expectException(\RuntimeException::class);
        TestContext::set();
    }

}
