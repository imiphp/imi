<?php
namespace Imi\Test\Tests;

use Imi\Test\BaseTest;
use Imi\App;

class InjectTest extends BaseTest
{
    public function testInject()
    {
        $test = App::getBean('TestInjectValue');
        $test->test();
    }

    public function testArg()
    {
        $test = App::getBean('TestArg');
        $test->test('123');
    }

}